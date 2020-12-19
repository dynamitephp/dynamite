<?php

declare(strict_types=1);

namespace Dynamite;

use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Marshaler;
use Doctrine\Common\Annotations\Reader;
use Dynamite\Exception\ItemRepositoryException;
use Dynamite\Mapping\ItemMapping;
use Dynamite\Mapping\ItemMappingReader;
use Psr\Log\LoggerInterface;

/**
 * @author pizzaminded <mikolajczajkowsky@gmail.com>
 * @license MIT
 */
class Dynamite
{

    /**
     * Configuration for all items in your projects
     * @var ItemMapping[]
     */
    protected array $itemMappings = [];
    protected DynamoDbClient $client;
    protected LoggerInterface $logger;
    protected Reader $annotationReader;
    protected TableConfiguration $tableConfiguration;
    protected array $managedObjects = [];
    private Marshaler $marshaler;
    protected ItemMappingReader $itemMappingReader;
    private ItemSerializer $itemSerializer;


    /**
     * @var array<string, ItemRepository>
     */
    private array $itemRepositories = [];

    private SingleTableService $singleTableService;

    /**
     * Dynamite constructor.
     * @param DynamoDbClient $client
     * @param LoggerInterface $logger
     * @param Reader $annotationReader
     * @param TableConfiguration $tableConfiguration
     * @param Marshaler|null $marshaler
     */
    public function __construct(
        DynamoDbClient $client,
        LoggerInterface $logger,
        Reader $annotationReader,
        TableConfiguration $tableConfiguration,
        ?Marshaler $marshaler = null
    )
    {
        $this->client = $client;
        $this->logger = $logger;
        $this->annotationReader = $annotationReader;
        $this->tableConfiguration = $tableConfiguration;
        $this->marshaler = $marshaler ?? new Marshaler();
        $this->itemMappingReader = new ItemMappingReader($this->annotationReader);
        $this->itemSerializer = new ItemSerializer($this->annotationReader);
        $this->singleTableService = new SingleTableService(
            $this->client,
            $this->tableConfiguration,
            $this->marshaler
        );
    }

    /**
     * @param string $itemClass
     * @return ItemRepository
     */
    public function getItemRepository(string $itemClass): ItemRepository
    {
        if (!isset($this->itemMappings[$itemClass])) {
            $this->itemMappings[$itemClass] = $this->itemMappingReader->getMappingFor($itemClass);
        }

        if (!isset($this->itemRepositories[$itemClass])) {
            $classToInstantiate = ItemRepository::class;
            $mapping = $this->itemMappings[$itemClass];

            $customRepositoryClass = $mapping->getCustomItemRepositoryClass();
            if ($customRepositoryClass !== null) {

                $extends = class_parents($customRepositoryClass);
                if (!isset($extends[ItemRepository::class])) {
                    throw new ItemRepositoryException('Classs "%s" cannot be used as a Item repository as it does not extend "%s" class.', $customRepositoryClass, $classToInstantiate);
                }

                $classToInstantiate = $customRepositoryClass;
            }

            $this->itemRepositories[$itemClass] = new $classToInstantiate(
                $this->singleTableService,
                $itemClass,
                $mapping,
                $this->itemSerializer
            );
        }

        return $this->itemRepositories[$itemClass];
    }

    /**
     * @return SingleTableService
     */
    public function getSingleTableService(): SingleTableService
    {
        return $this->singleTableService;
    }
}
