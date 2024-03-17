<?php


namespace Dynamite\Fixtures;

use Dynamite\Configuration as Dynamite;

#[Dynamite\Item(objectType:"DUMMY")]
#[Dynamite\PartitionKeyFormat("DUMMY#")]
class DummyItemWithPartitionKeyFormat
{

}