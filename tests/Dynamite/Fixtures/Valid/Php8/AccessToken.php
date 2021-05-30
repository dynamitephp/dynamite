<?php


namespace Dynamite\Fixtures\Valid\Php8;

use Dynamite\Configuration as Dynamite;

#[Dynamite\Item(objectType: 'acstkn')]
#[Dynamite\PartitionKeyFormat('O2ACCTKN#{id}')]
#[Dynamite\SortKeyFormat('O2ACCTKN')]
class AccessToken
{

    #[Dynamite\PartitionKey]
    protected $pk;

    #[Dynamite\Attribute('identifier', type: 'string')]
    protected string $id;

}