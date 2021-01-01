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
class DuplicateTo
{
    public string $pk;

    public ?string $sk;

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
}