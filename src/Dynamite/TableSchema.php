<?php
declare(strict_types=1);

namespace Dynamite;

use Dynamite\Exception\DynamiteException;

/**
 * Holds basic information about DynamoDB table structure.
 *
 * @author pizzaminded <mikolajczajkowsky@gmail.com>
 * @license MIT
 */
class TableSchema
{

    /**
     * @param string $tableName
     * @param string $partitionKeyName
     * @param string $sortKeyName
     * @param array $indexes
     * @psalm-param array<string, array{pk: string, sk: string|null}> $indexes
     */
    public function __construct(
        protected string $tableName,
        protected string $partitionKeyName = 'pk',
        protected string $sortKeyName = 'sk',
        protected array $indexes = [],
        protected string $objectTypeAttrName = 'objectType'
    )
    {}

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

    /**
     * @throws DynamiteException
     */
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

    /**
     * @return string
     */
    public function getObjectTypeAttrName(): string
    {
        return $this->objectTypeAttrName;
    }
}