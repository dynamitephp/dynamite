<?php
declare(strict_types=1);

namespace Dynamite\Fixtures;

use Dynamite\Configuration as Dynamite;

/**
 * @Dynamite\Item(objectType="DUMMY")
 * @Dynamite\PartitionKeyFormat("DUMMY#{id}")
 */
class DummyFullWithMissingSortKeyFormat
{

    private string $id;

    /**
     * @Dynamite\PartitionKey()
     * @var string
     */
    private $pk;

    /**
     * @Dynamite\SortKey()
     * @var string
     */
    private $sk;

}