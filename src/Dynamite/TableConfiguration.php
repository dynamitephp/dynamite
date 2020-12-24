<?php
declare(strict_types=1);

namespace Dynamite;

use Dynamite\Exception\DynamiteException;

/**
 * @author pizzaminded <mikolajczajkowsky@gmail.com>
 * @license MIT
 */
class TableConfiguration
{
    protected string $tableName;
    protected string $partitionKeyName;
    protected string $sortKeyName;
    protected array $indexes = [];

    public function __construct(
        string $tableName,
        string $partitionKeyName = 'pk',
        string $sortKeyName = 'sk',
        array $indexes = []
    )
    {
        $this->tableName = $tableName;
        $this->partitionKeyName = $partitionKeyName;
        $this->sortKeyName = $sortKeyName;
        $this->indexes = $indexes;
    }

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * @return string
     */
    public function getPartitionKeyName(): string
    {
        return $this->partitionKeyName;
    }

    /**
     * @return string
     */
    public function getSortKeyName(): string
    {
        return $this->sortKeyName;
    }

    public function getIndexPrimaryKeyPair(string $indexName): array
    {
        if (!isset($this->indexes[$indexName])) {
            throw new DynamiteException(sprintf('Index with name "%s" not found in table configuration', $indexName));
        }

        return [
            $this->indexes[$indexName]['pk'],
            $this->indexes[$indexName]['sk'] ?? null,
        ];

    }
}