<?php
declare(strict_types=1);

namespace Dynamite;

use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Marshaler;
use Aws\DynamoDb\WriteRequestBatch;
use Aws\Exception\AwsException;
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
    public function __construct(
        protected DynamoDbClient $client,
        protected TableSchema $schema,
        protected Marshaler $marshaler,
        protected LoggerInterface $logger
    )
    {
    }


    public function putItem(string $partitionKeyVal, array $item, ?string $sortKeyValue = null): void
    {
        $item[$this->schema->getPartitionKeyName()] = $partitionKeyVal;
        $item[$this->schema->getSortKeyName()] = $sortKeyValue;

        $putItemRequest = [
            'TableName' => $this->schema->getTableName(),
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
        $partitionKeyAttr = $this->schema->getPartitionKeyName();
        $sortKeyAttr = $this->schema->getSortKeyName();

        if ($index !== null) {
            [$partitionKeyAttr, $sortKeyAttr] = $this->schema->getIndexPrimaryKeyPair($index);
        }

        $tableName = $this->schema->getTableName();
        $debugMessage = sprintf('Executing Query operation on table "%s" ', $tableName);

        $queryRequest = [
            'TableName' => $this->schema,
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
        $request->withTableName($this->schema->getTableName());

        return new QueryResponse(
            $this->client->query($request->toArray())->toArray(),
            $this->marshaler
        );
    }

    /**
     * @TODO: rename to getTableSchema
     * @TODO: is this really required to be exposed?
     *
     * @return TableSchema
     */
    public function getTableConfiguration(): TableSchema
    {
        return $this->schema;
    }


    public function getItem(string $pk, ?string $sk = null): ?array
    {
        $key = [
            $this->schema->getPartitionKeyName() => $this->marshaler->marshalValue($pk)
        ];

        if ($sk !== null) {
            $key[$this->schema->getSortKeyName()] = $this->marshaler->marshalValue($sk);
        }

        $request = [
            'TableName' => $this->schema->getTableName(),
            'Key' => $key
        ];

        $result = $this->client->getItem($request)->toArray();

        if (!isset($result['Item'])) {
            return null;
        }
        return $this->unmarshalItem($result['Item']);
    }

    /**
     * @param array $itemsToPut
     * @param array $itemsToDelete
     * @throws AwsException
     */
    public function writeRequestBatch(
        array $itemsToPut = [],
        array $itemsToDelete = []
    )
    {
        $writeRequestBatch = new WriteRequestBatch($this->client, [
            'table' => $this->schema->getTableName(),
            'error' => function (AwsException $exception) {
                /**
                 * By default it does not throw all exceptions
                 */
                throw $exception;
            }
        ]);

        foreach ($itemsToPut as $item) {
            $writeRequestBatch->put(
                $this->marshaler->marshalItem($item),
                $this->schema->getTableName()
            );
        }

        foreach ($itemsToDelete as $item) {
            $writeRequestBatch->delete(
                $this->marshaler->marshalItem($item),
                $this->schema->getTableName()
            );
        }

        $writeRequestBatch->flush();
    }
}