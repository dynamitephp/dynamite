<?php
declare(strict_types=1);

namespace Dynamite;

/**
 * @author pizzaminded <mikolajczajkowsky@gmail.com>
 * @license MIT
 */
class AccessPattern
{
    protected string $name;
    protected ?string $index = null;
    protected string $partitionKeyFormat;
    protected ?string $sortKeyFormat = null;
    protected ?int $limit = null;


    protected function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function create(string $name): self
    {
        return new self($name);
    }


    /**
     * @return static
     */
    public function withIndex(string $index): self
    {
        $self = clone $this;
        $self->index = $index;
        return $self;
    }

    /**
     * @return static
     */
    public function withPartitionKeyFormat(string $pk): self
    {
        $self = clone $this;
        $self->partitionKeyFormat = $pk;
        return $self;
    }

    /**
     * @return static
     */
    public function withSortKeyFormat(string $sk): self
    {
        $self = clone $this;
        $self->sortKeyFormat = $sk;
        return $self;
    }


    public function withLimit(int $limit)
    {
        $self = clone $this;
        $self->limit = $limit;
        return $limit;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function getIndex(): ?string
    {
        return $this->index;
    }

    /**
     * @return string
     */
    public function getPartitionKeyFormat(): string
    {
        return $this->partitionKeyFormat;
    }

    /**
     * @return string|null
     */
    public function getSortKeyFormat(): ?string
    {
        return $this->sortKeyFormat;
    }

    /**
     * @return int|null
     */
    public function getLimit(): ?int
    {
        return $this->limit;
    }


}