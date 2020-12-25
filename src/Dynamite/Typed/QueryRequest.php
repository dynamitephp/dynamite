<?php
declare(strict_types=1);

namespace Dynamite\Typed;

/**
 * @author pizzaminded <mikolajczajkowsky@gmail.com>
 * @see https://docs.aws.amazon.com/amazondynamodb/latest/APIReference/API_Query.html
 * @license MIT
 */
class QueryRequest
{
    protected string $tableName;
    protected ?string $indexName = null;
    protected ?array $exclusiveStartKey = null;
    protected ?int $limit = null;

    public function toArray(): array
    {
        $output = [];
        $output['TableName'] = $this->tableName;

        if($this->limit !== null) {
            $output['Limit'] = $this->limit;
        }

        if($this->indexName !== null) {
            $output['IndexName'] = $this->indexName;
        }

        if($this->exclusiveStartKey !== null) {
            $output['ExclusiveStartKey'] = $this->exclusiveStartKey;
        }

        return $output;
    }


    public function withTableName(string $tableName): self
    {
        $this->tableName = $tableName;
        return $this;
    }

    public function withIndexName(string $indexName): self
    {
        $this->indexName = $indexName;
        return $this;
    }

    public function withExclusiveStartKey(array $key): self
    {
        $this->exclusiveStartKey = $key;
        return $this;
    }


}