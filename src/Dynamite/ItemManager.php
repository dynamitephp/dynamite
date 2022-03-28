<?php

declare(strict_types=1);

namespace Dynamite;

use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Marshaler;
use Doctrine\Common\Annotations\Reader;
use Dynamite\Exception\ItemRepositoryException;
use Dynamite\Mapping\ItemMapping;
use Dynamite\Mapping\ItemMappingReader;
use Dynamite\PrimaryKey\KeyFormatResolver;
use Psr\Log\LoggerInterface;

/**
 * @author pizzaminded <mikolajczajkowsky@gmail.com>
 * @license MIT
 */
class ItemManager
{

    /**
     * Configuration for all items managed by given instance.
     *
     * @var ItemMapping[]
     */
    protected array $itemMappings = [];

    /**
     * @var array<string, ItemRepository>
     */
    private array $itemRepositories = [];

    private SingleTableService $singleTableService;

    public function __construct(
        protected DynamoDbClient $client,
        protected TableSchema $tableSchema,
        protected ItemMappingReader $mappingReader,
        protected array $managedObjects,
        protected ItemSerializer $itemSerializer,
        protected KeyFormatResolver $keyFormatResolver,
        protected ?LoggerInterface $logger = null,
        protected ?Marshaler $marshaler = null,
    ) {
        $this->singleTableService = new SingleTableService(
            $this->client,
            $this->tableSchema,
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
                $this->itemSerializer,
                $this->keyFormatResolver
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
