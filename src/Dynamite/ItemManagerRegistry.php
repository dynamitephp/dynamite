<?php

declare(strict_types=1);

namespace Dynamite;

use Dynamite\Exception\DynamiteException;

class ItemManagerRegistry
{
    /**
     * @var ItemManager[]
     */
    protected array $managedTables = [];

    /**
     * @param ItemManager $instance
     */
    public function addManagedTable(ItemManager $instance): void
    {
        $this->managedTables[] = $instance;
    }

    public function getItemRepositoryFor(string $fqcn): ItemRepository
    {
        foreach ($this->managedTables as $instance) {
            if ($instance->manages($fqcn)) {
                return $instance->getItemRepository($fqcn);
            }
        }

        throw new DynamiteException(sprintf('Could not find any instance which manage "%s" object.', $fqcn));
    }
}