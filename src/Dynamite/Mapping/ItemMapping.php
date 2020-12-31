<?php
declare(strict_types=1);

namespace Dynamite\Mapping;

use Dynamite\Configuration\AttributeInterface;
use Dynamite\Configuration\Item;
use Dynamite\Configuration\NestedItem;

/**
 * @author pizzaminded <mikolajczajkowsky@gmail.com>
 * @license MIT
 */
class ItemMapping
{
    private Item $item;
    private Key $partitionKey;
    private ?Key $sortKey;
    /**
     * @var AttributeInterface[]
     * @psalm-var array<string, AttributeInterface>
     */
    private array $propertiesMapping;

    /**
     * @var NestedItem[]
     * @psalm-var array<string, NestedItem>
     */
    private array $nestedItems;

    public function __construct(
        Item $item,
        Key $partitionKey,
        array $propertiesMapping,
        ?Key $sortKey = null,
        array $nestedItems = []
    )
    {
        $this->item = $item;
        $this->partitionKey = $partitionKey;
        $this->propertiesMapping = $propertiesMapping;
        $this->sortKey = $sortKey;
        $this->nestedItems = $nestedItems;
    }

    public function getObjectType(): string
    {
        return $this->item->getObjectType();
    }

    /**
     * @return string
     */
    public function getPartitionKeyProperty(): string
    {
        return $this->partitionKey->getProperty();
    }

    /**
     * @return string
     */
    public function getPartitionKeyFormat(): string
    {
        return $this->partitionKey->getKeyFormat();
    }

    /**
     * @return AttributeInterface[]
     * @psalm-return array<string,AttributeInterface>
     */
    public function getPropertiesMapping(): array
    {
        return $this->propertiesMapping;
    }

    public function getCustomItemRepositoryClass(): ?string
    {
        return $this->item->getRepositoryClass();
    }

    public function getSortKeyFormat(): ?string
    {
        if ($this->sortKey === null) {
            return null;
        }

        return $this->sortKey->getKeyFormat();
    }

    public function getSortKeyProperty(): ?string
    {
        if ($this->sortKey === null) {
            return null;
        }

        return $this->sortKey->getProperty();
    }

    public function getNestedItems(): array
    {
        return $this->nestedItems;
    }
}