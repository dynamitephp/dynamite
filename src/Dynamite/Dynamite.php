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
     * @param array $managedObjects
     * @param Marshaler|null $marshaler
     * @param ItemMappingReader|null $mappingReader
     */
    public function __construct(
        DynamoDbClient $client,
        LoggerInterface $logger,
        Reader $annotationReader,
        TableConfiguration $tableConfiguration,
        array $managedObjects,
        ?Marshaler $marshaler = null,
        ItemMappingReader $mappingReader = null
    )
    {
        $this->client = $client;
        $this->logger = $logger;
        $this->annotationReader = $annotationReader;
        $this->tableConfiguration = $tableConfiguration;
        $this->managedObjects = $managedObjects;
        $this->marshaler = $marshaler ?? new Marshaler();
        $this->itemMappingReader = $mappingReader ?? new ItemMappingReader($this->annotationReader);
        $this->itemSerializer = new ItemSerializer();
        $this->singleTableService = new SingleTableService(
            $this->client,
            $this->tableConfiguration,
            $this->marshaler,
            $this->logger
        );
    }

    /**
     * @param string $itemClass
     * @return ItemRepository
     */
    public function getItemRepository(string $itemClass): ItemRepository
    {
        if (!$this->manages($itemClass)) {
            throw ItemRepositoryException::objectNotManaged($itemClass);
        }

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

    public function manages(string $objectFqcn): bool
    {
        return in_array($objectFqcn, $this->managedObjects, true);
    }
}
