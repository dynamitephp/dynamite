<?php
declare(strict_types=1);

namespace Dynamite\Bundle\DependencyInjection;


use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{

    /**
     * @inheritDoc
     */
    public function getConfigTreeBuilder()
    {
        $builder = new TreeBuilder('dynamite');
        $builder
            ->getRootNode()
            ->children()
                ->scalarNode('annotation_reader_id')
                    ->info('doctrine/annotations reader instance')
                    ->defaultValue('annotation_reader')
                ->end()
                ->arrayNode('tables')
                    ->useAttributeAsKey('table')
                        ->arrayPrototype()
                            ->children()
                                ->scalarNode('connection')->defaultValue('Aws\DynamoDb\DynamoDbClient')->end()
                                ->scalarNode('table_name')->isRequired()->end()
                                ->booleanNode('default')->isRequired()->end()
                                ->scalarNode('partition_key_name')->defaultValue('pk')->end()
                                ->scalarNode('sort_key_name')->defaultValue('sk')->end()
                                ->arrayNode('managed_items')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
        return $builder;
    }
}