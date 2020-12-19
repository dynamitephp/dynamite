<?php
declare(strict_types=1);

namespace Dynamite;

use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Marshaler;

/**
 * A wrapper for DynamoDbClient which knows your table configuration so you do not have to inject them anywhere.
 * It has a marshaller too, so you can just put your data here and this class will do the rest.
 *
 * @author pizzaminded <mikolajczajkowsky@gmail.com>
 * @license MIT
 */
class SingleTableService
{

    private Marshaler $marshaler;
    private TableConfiguration $table;
    private DynamoDbClient $client;

    public function __construct(DynamoDbClient $client, TableConfiguration $table, Marshaler $marshaler)
    {
        $this->client = $client;
        $this->table = $table;
        $this->marshaler = $marshaler;
    }


    public function putItem(string $partitionKeyVal, array $item, ?string $sortKeyValue = null): void
    {
        $item[$this->table->getPartitionKeyName()] = $partitionKeyVal;
        $item[$this->table->getSortKeyName()] = $sortKeyValue;

        $putItemRequest = [
            'TableName' => $this->table->getTableName(),
            'Item' => $this->marshaler->marshalItem($item)
        ];

        $this->client->putItem($putItemRequest);
    }

    /**
     * @return (\stdClass|array)[]
     *
     * @psalm-return list<\stdClass|array>
     */
    public function simpleQuery(string $pk, ?string $sk = null, ?string $index = null): array
    {
        $partitionKeyAttr = $this->table->getPartitionKeyName();
        $sortKeyAttr = $this->table->getSortKeyName();

        if ($index !== null) {
            [$partitionKeyAttr, $sortKeyAttr] = $this->table->getIndexPrimaryKeyPair($index);
        }

        $queryRequest = [
            'TableName' => $this->table->getTableName(),
            'KeyConditionExpression' => '#pk = :pk',
            'ExpressionAttributeNames' => [
                '#pk' => $partitionKeyAttr
            ],
            'ExpressionAttributeValues' => [
                ':pk' => $this->marshaler->marshalValue($pk)
            ]
        ];

        if ($index !== null) {
            $queryRequest['IndexName'] = $index;
        }

        /** @psalm-var array<array-key, mixed> $items */
        $items = $this->client->query($queryRequest)->toArray()['Items'];
        $output = [];

        foreach ($items as $item) {
            $output[] = $this->marshaler->unmarshalItem($item);
        }

        return $output;
    }

}