<?php
declare(strict_types=1);

namespace Dynamite\Typed;

use Aws\DynamoDb\Marshaler;

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
    protected ?string $keyConditionExpression = null;
    protected array $expressionAttributeValues = [];
    protected array $expressionAttributeNames = [];
    protected Marshaler $marshaler;

    public function __construct(?Marshaler $marshaler = null)
    {
        $this->marshaler = $marshaler ?? new Marshaler();
    }

    public function toArray(): array
    {
        $output = [];
        $output['TableName'] = $this->tableName;

        if ($this->limit !== null) {
            $output['Limit'] = $this->limit;
        }

        if ($this->indexName !== null) {
            $output['IndexName'] = $this->indexName;
        }

        if ($this->exclusiveStartKey !== null) {
            $output['ExclusiveStartKey'] = $this->exclusiveStartKey;
        }

        if ($this->keyConditionExpression !== null) {
            $output['KeyConditionExpression'] = $this->keyConditionExpression;
        }

        if (count($this->expressionAttributeNames) > 0) {
            $output['ExpressionAttributeNames'] = $this->expressionAttributeNames;
        }

        if (count($this->expressionAttributeValues) > 0) {
            $output['ExpressionAttributeValues'] =
                $this->marshaler->marshalItem(
                    $this->expressionAttributeValues
                );
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

    public function withLimit(int $limit)
    {
        $this->limit = $limit;
        return $this;
    }

    public function withKeyConditionExpression(string $expr)
    {
        $this->keyConditionExpression = $expr;
        return $this;
    }

    public function withExpressionAttributeName($placeholder, $val)
    {
        $this->expressionAttributeNames[$placeholder] = $val;
        return $this;
    }

    public function withExpressionAttributeValue($placeholder, $val)
    {
        $this->expressionAttributeValues[$placeholder] = $val;
        return $this;
    }
}