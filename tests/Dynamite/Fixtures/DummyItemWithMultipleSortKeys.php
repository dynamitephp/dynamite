<?php
declare(strict_types=1);

namespace Dynamite\Fixtures;

use Dynamite\Configuration as Dynamite;

/**
 * @Dynamite\Item(objectType="DUMMY")
 * @Dynamite\PartitionKeyFormat("DUMMY#")
 */
class DummyItemWithMultipleSortKeys
{

    /**
     * @Dynamite\SortKey()
     * @var string
     */
    private string $sk1;

    /**
     * @Dynamite\SortKey()
     * @var string
     */
    private string $sk2;
}