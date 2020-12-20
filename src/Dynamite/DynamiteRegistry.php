<?php
declare(strict_types=1);

namespace Dynamite;


class DynamiteRegistry
{

    /**
     * @var array<string,Dynamite>
     */
    protected array $managedTables = [];

    /**
     * @param string $name
     * @param Dynamite $instance
     */
    public function addManagedTable(string $name, Dynamite $instance)
    {
        $this->managedTables[$name] = $instance;
    }
}