<?php
declare(strict_types=1);

namespace Dynamite;

use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Marshaler;
use Dynamite\Typed\QueryRequest;
use Dynamite\Typed\QueryResponse;
use Psr\Log\LoggerInterface;

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
    protected LoggerInterface $logger;

    public function __construct(
        DynamoDbClient $client,
        TableConfiguration $table,
        Marshaler $marshaler,
        LoggerInterface $logger
    )
    {
        $this->client = $client;
        $this->table = $table;
        $this->marshaler = $marshaler;
        $this->logger = $logger;
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
    public function simpleQuery(string $pk, ?string $sk = null, ?string $index = null): QueryResponse
    {
        $partitionKeyAttr = $this->table->getPartitionKeyName();
        $sortKeyAttr = $this->table->getSortKeyName();

        if ($index !== null) {
            [$partitionKeyAttr, $sortKeyAttr] = $this->table->getIndexPrimaryKeyPair($index);
        }

        $tableName = $this->table->getTableName();
        $debugMessage = sprintf('Executing Query operation on table "%s" ', $tableName);

        $queryRequest = [
            'TableName' => $this->table,
            'KeyConditionExpression' => '#pk = :pk',
            'ExpressionAttributeNames' => [
                '#pk' => $partitionKeyAttr
            ],
            'ExpressionAttributeValues' => [
                ':pk' => $this->marshaler->marshalValue($pk)
            ]
        ];

        if ($index !== null) {
            $debugMessage = sprintf('%s and "%s" index' . $debugMessage, $index);
            $queryRequest['IndexName'] = $index;
        }

        $this->logger->debug($debugMessage);
        /** @psalm-var array<array-key, mixed> $items */
        $response = $this->client->query($queryRequest)->toArray();

        return new QueryResponse($response, $this->marshaler);
    }


    public function unmarshalItem(array $item): array
    {
        return $this->marshaler->unmarshalItem($item);
    }

    public function rawQuery(QueryRequest $request): QueryResponse
    {
        $request->withTableName($this->table->getTableName());

        return new QueryResponse(
            $this->client->query($request->toArray())->toArray(),
            $this->marshaler
        );
    }

    public function getTableConfiguration(): TableConfiguration
    {
        return $this->table;
    }


    public function getItem(string $pk, ?string $sk = null): ?array
    {
        $key = [
            $this->getTableConfiguration()->getPartitionKeyName() => $this->marshaler->marshalValue($pk)
        ];

        if ($sk !== null) {
            $key[$this->getTableConfiguration()->getSortKeyName()] = $this->marshaler->marshalValue($sk);
        }

        $request = [
            'TableName' => $this->getTableConfiguration()->getTableName(),
            'Key' => $key
        ];

        $result = $this->client->getItem($request)->toArray();

        if (!isset($result['Item'])) {
            return null;
        }
        return $this->unmarshalItem($result['Item']);
    }
}