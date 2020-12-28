<?php
declare(strict_types=1);

namespace Dynamite\Fixtures;
use Dynamite\Configuration as Dynamite;

/**
 * @Dynamite\Item(objectType="DUMMY")
 * @Dynamite\PartitionKeyFormat("DUMMY#")
 */
class DummyItemWithMultiplePartitionKeys
{

    /**
     * @Dynamite\PartitionKey()
     * @var string
     */
    private string $pk1;

    /**
     * @Dynamite\PartitionKey()
     * @var string
     */
    private string $pk2;
}