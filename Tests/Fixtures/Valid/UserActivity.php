<?php
declare(strict_types=1);

namespace Dynamite\Tests\Fixtures\Valid;

use Dynamite\Configuration as Dynamite;

/**
 * @Dynamite\Item(objectType="USERACTIVITY")
 * @Dynamite\PartitionKeyFormat("USERACTIVITY#{userId}")
 * @Dynamite\SortKeyFormat("ACT#{activityId}")
 */
class UserActivity
{

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

    /**
     * @Dynamite\Attribute(type="string", name="userId")
     * @var string
     */
    private string $userId;

    /**
     * @Dynamite\Attribute(type="string", name="activityId")
     * @var string
     */
    private string $activityId;
}