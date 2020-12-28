<?php
declare(strict_types=1);

namespace Dynamite\ServiceProvider;


use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Marshaler;
use Dynamite\Dynamite;
use Dynamite\DynamiteRegistry;
use Dynamite\TableConfiguration;
use Jadob\Bridge\Doctrine\Annotations\ServiceProvider\DoctrineAnnotationsProvider;
use Jadob\Container\Container;
use Jadob\Container\ServiceProvider\ParentProviderInterface;
use Jadob\Container\ServiceProvider\ServiceProviderInterface;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class DynamiteProvider implements ServiceProviderInterface, ParentProviderInterface
{

    /**
     * @inheritDoc
     */
    public function getConfigNode()
    {
        return 'dynamite';
    }

    /**
     * @inheritDoc
     */
    public function register($config)
    {
        $output = [];
        $annotationReaderId = 'doctrine.annotations.reader';
        if (isset($config['annotation_reader_id'])) {
            $annotationReaderId = $config['annotation_reader_id'];
        }

        $output['dynamite.logger'] = static function (ContainerInterface $container): LoggerInterface {
            /** @noinspection MissingService */
            return new Logger('dynamite', [$container->get('logger.handler.default')]);
        };

        $output[DynamiteRegistry::class] = static function (): DynamiteRegistry {
            return new DynamiteRegistry();
        };

        foreach ($config['tables'] as $instanceName => $table) {
            $instanceDef = static function (ContainerInterface $container) use ($table, $annotationReaderId): Dynamite {
                $clientId = DynamoDbClient::class;

                if (isset($table['connection'])) {
                    $clientId = $table['connection'];
                }

                $tableConfiguration = new TableConfiguration(
                    $table['table_name'],
                    $table['partition_key_name'],
                    $table['sort_key_name'],
                    $table['indexes'] ?? []
                );

                /** @noinspection MissingService */
                return new Dynamite(
                    $container->get($clientId),
                    $container->get('dynamite.logger'),
                    $container->get($annotationReaderId),
                    $tableConfiguration,
                    $table['managed_objects'],
                    new Marshaler()
                );
            };

            $output[sprintf('dynamite.%s', $instanceName)] = $instanceDef;
        }

        return $output;
    }

    /**
     * @inheritDoc
     */
    public function onContainerBuild(Container $container, $config)
    {
        // TODO: Implement onContainerBuild() method.
    }

    /**
     * @inheritDoc
     */
    public function getParentProviders()
    {
        return [
            DoctrineAnnotationsProvider::class
        ];
    }
}