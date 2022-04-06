<?php
declare(strict_types=1);

namespace Dynamite\Configuration;


use Doctrine\Common\Annotations\Annotation\Target;

/**
 * When spotted in configuration, takes props from main item and writes them to new record.
 * This enforces persisting items via batchWriteItem.
 *
 * @Annotation
 * @Target({"CLASS"})
 *
 * @author pizzaminded <mikolajczajkowsky@gmail.com>
 * @license MIT
 */
#[\Attribute(flags: \Attribute::TARGET_CLASS)]
class DuplicateTo
{
    public string $pk;

    public ?string $sk;

    /**
     * Applies transformations to Primary key attributes.
     * @Enum({"UPPER", "LOWER"})
     * @deprecated - soon to be removed - use {upper:propName} or {lower:propName} filter
     */
    public ?string $transform = null;

    public array $props = [];

    /**
     * @return array
     */
    public function getProps(): array
    {
        return $this->props;
    }

    public function getPartitionKeyFormat(): string
    {
        return $this->pk;
    }

    public function getSortKeyFormat(): string
    {
        return $this->sk;
    }

    /**
     * @return string|null
     */
    public function getTransform(): ?string
    {
        return $this->transform;
    }

}