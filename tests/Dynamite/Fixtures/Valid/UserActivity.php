<?php
declare(strict_types=1);

namespace Dynamite\Fixtures\Valid;

use Dynamite\Configuration as Dynamite;

#[Dynamite\Item(objectType:"USERACTIVITY")]
#[Dynamite\PartitionKeyFormat("USERACTIVITY#{userId}")]
#[Dynamite\SortKeyFormat("ACT#{activityId}")]
class UserActivity
{

    #[Dynamite\PartitionKey()]
    private $pk;

    #[Dynamite\SortKey()]
    private $sk;

    #[Dynamite\Attribute(type:"string", name:"userId")]
    private string $userId;


    #[Dynamite\Attribute(type:"string", name:"activityId")]
    private string $activityId;
}