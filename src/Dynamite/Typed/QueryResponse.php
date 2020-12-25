<?php
declare(strict_types=1);

namespace Dynamite\Typed;


use Aws\DynamoDb\Marshaler;

class QueryResponse
{
    protected Marshaler $marshaler;
    protected ?ConsumedCapacity $consumedCapacity;
    protected int $count;
    protected array $items;
    protected ?array $lastEvaluatedKey = null;
    protected ?int $scannedCount = null;

    /**
     * QueryResponse constructor.
     * @param array $response
     * @param Marshaler|null $marshaler
     */
    public function __construct(array $response, ?Marshaler $marshaler)
    {
        $this->consumedCapacity = isset($response['ConsumedCapacity'])
            ? ConsumedCapacity::fromArray($response['ConsumedCapacity'])
            : null;
        $this->count = $response['Count'];
        $this->items = $response['Items'];
        $this->lastEvaluatedKey = $response['LastEvaluatedKey'] ?? null;
        $this->scannedCount = $response['ScannedCount'] ?? null;
        $this->marshaler = $marshaler ?? new Marshaler();
    }

    /**
     * Returns marshaled items.
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @return array|null
     */
    public function getLastEvaluatedKey()
    {
        return $this->lastEvaluatedKey;
    }


}