<?php
declare(strict_types=1);

namespace Dynamite\Bundle\DependencyInjection;


use Dynamite\Dynamite;
use Dynamite\ItemManagerRegistry;
use Dynamite\TableConfiguration;
use Monolog\Logger;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author pizzaminded <mikolajczajkowsky@gmail.com>
 */
class DynamiteExtension extends Extension
{
    private const INSTANCE_ID = 'dynamite.%s';
//    private const INSTANCE_STS_ID = 'dynamite.%s.service';
    private const LOGGER_NAME = 'dynamite.%s';

    /**
     * @inheritDoc
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configObject = new Configuration();
        $config = $this->processConfiguration($configObject, $configs);

        $registryDefinition = new Definition(ItemManagerRegistry::class);
        $annotationReaderRef = new Reference($config['annotation_reader_id']);

        foreach ($config['tables'] as $instanceName => $instanceConfiguration) {
            $instanceLogger = new Definition(Logger::class);
            $instanceLogger->setArgument('$name', sprintf(self::LOGGER_NAME, $instanceName));
            $instanceLogger->setArgument('$handlers', [new Reference('monolog.handler.main')]);
            $instanceLogger->setPrivate(true);
            $container->setDefinition(sprintf('dynamite.logger.%s', $instanceName), $instanceLogger);


            $tableConfigurationId = sprintf('dynamite.table_configuration.%s', $instanceName);
            $tableConfigurationDefinition = new Definition(TableConfiguration::class);
            $tableConfigurationDefinition->setArgument('$tableName', $instanceConfiguration['table_name']);
            $tableConfigurationDefinition->setArgument('$partitionKeyName', $instanceConfiguration['partition_key_name']);
            $tableConfigurationDefinition->setArgument('$sortKeyName', $instanceConfiguration['sort_key_name']);
            $tableConfigurationDefinition->setArgument('$indexes', $instanceConfiguration['indexes']);
            $tableConfigurationDefinition->setPrivate(true);
            $container->setDefinition($tableConfigurationId, $tableConfigurationDefinition);


            $instanceDefinition = new Definition(Dynamite::class);
            $instanceDefinition->setArgument('$client', new Reference($instanceConfiguration['connection']));
            $instanceDefinition->setArgument('$logger', new Reference(sprintf('dynamite.logger.%s', $instanceName)));
            $instanceDefinition->setArgument('$annotationReader', $annotationReaderRef);
            $instanceDefinition->setArgument('$managedObjects', $instanceConfiguration['managed_items']);
            $instanceDefinition->setPublic(true);
            $instanceDefinition->setArgument('$tableConfiguration', new Reference($tableConfigurationId));

            $container->setDefinition(Dynamite::class, $instanceDefinition);
            $container->setAlias(sprintf(self::INSTANCE_ID, $instanceName), Dynamite::class);

            $registryDefinition->addMethodCall('addManagedTable', [$instanceName, $instanceDefinition]);
        }
        $container->setDefinition('dynamite.registry', $registryDefinition);
    }
}