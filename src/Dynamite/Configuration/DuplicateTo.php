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
#[\Attribute(flags: \Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
class DuplicateTo
{
    public function __construct(
        protected string $pk,
        protected ?string $sk,
        /**
         * Properties from base entity that should be put in duplicated item
         * @var array<string, mixed>
        */
        protected array $props = []
    )
    {
    }

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