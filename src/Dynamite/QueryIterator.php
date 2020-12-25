<?php
declare(strict_types=1);

namespace Dynamite;

use Countable;
use IteratorAggregate;

class QueryIterator implements Countable, IteratorAggregate
{

    protected array $items;

    protected ?array $lastEvaluatedKey;

    public function __construct(array $items, ?array $lastEvaluatedKey = null)
    {
        $this->items = $items;
        $this->lastEvaluatedKey = $lastEvaluatedKey;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->items);
    }

    public function count()
    {
        return count($this->items);
    }
}