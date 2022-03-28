<?php

declare(strict_types=1);

namespace Dynamite;

use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Marshaler;
use Doctrine\Common\Annotations\Reader;
use Dynamite\Mapping\ItemMappingReader;
use Dynamite\PrimaryKey\Filter\LowercaseFilter;
use Dynamite\PrimaryKey\Filter\Md5Filter;
use Dynamite\PrimaryKey\Filter\UppercaseFilter;
use Dynamite\PrimaryKey\Filter\UppercaseFirstFilter;
use Dynamite\PrimaryKey\KeyFormatResolver;
use Psr\Log\LoggerInterface;

/**
 * @license MIT
 */
class ItemManagerRegistryFactory
{
    protected ?LoggerInterface $logger = null;
    protected ?Reader $annotationReader = null;
    protected ?Marshaler $marshaler = null;
    protected ?KeyFormatResolver $keyFormatResolver = null;
    protected array $valueFilters = [];
    /** @psalm-var array{DynamoDbClient, TableSchema, class-string[]}[] */
    protected array $itemManagers = [];
    protected bool $build = false;

    /**
     * @param string[] $managedItems
     * @psalm-param class-string[] $managedItems
     */
    public function addNativeDynamoDbClientItemManager(
        DynamoDbClient $dynamoDbClient,
        TableSchema $tableSchema,
        array $managedItems,
    ) {
        $this->itemManagers[] = [
            $dynamoDbClient,
            $tableSchema,
            $managedItems,
        ];
    }

    public function build(): ItemManagerRegistry
    {
        $itemMappingReader = new ItemMappingReader($this->annotationReader);
        $itemSerializer = new ItemSerializer();
        $marshaler = $this->marshaler ?? new Marshaler();
        $keyFormatResolver = new KeyFormatResolver();
        $keyFormatResolver->addFilter('md5', new Md5Filter());
        $keyFormatResolver->addFilter('lower', new LowercaseFilter());
        $keyFormatResolver->addFilter('upper', new UppercaseFilter());
        $keyFormatResolver->addFilter('ucfirst', new UppercaseFirstFilter());

        $registry = new ItemManagerRegistry();

        foreach ($this->itemManagers as $itemManagerDeps) {
            /** @var DynamoDbClient $dynamoDbClient */
            [$dynamoDbClient, $tableSchema, $managedItems] = $itemManagerDeps;

            $registry->addManagedTable(
                new ItemManager(
                    $dynamoDbClient,
                    $tableSchema,
                    $itemMappingReader,
                    $managedItems,
                    $itemSerializer,
                    $keyFormatResolver,
                    $this->logger,
                    $marshaler
                )
            );
        }

        return $registry;
    }

    public function withLogger(LoggerInterface $logger): self
    {
        $this->logger = $logger;

        return $this;
    }

    public function withMarshaler(Marshaler $marshaler): self
    {
        $this->marshaler = $marshaler;

        return $this;
    }

    public function withAnnotationReader(Reader $reader): self
    {
        $this->annotationReader = $reader;

        return $this;
    }
}
