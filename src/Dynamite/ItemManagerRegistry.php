<?php
declare(strict_types=1);

namespace Dynamite;


use Dynamite\Exception\DynamiteException;

class ItemManagerRegistry
{

    /**
     * @var ItemManager[]
     * @psalm-var array<string,Dynamite>
     */
    protected array $managedTables = [];

    /**
     * @param string $name
     * @param ItemManager $instance
     */
    public function addManagedTable(string $name, ItemManager $instance)
    {
        $this->managedTables[$name] = $instance;
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