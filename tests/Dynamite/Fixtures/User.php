<?php

namespace Dynamite\Fixtures;

use Dynamite\Configuration\Item;
use Dynamite\Configuration\NestedItemAttribute;
use Dynamite\Configuration\PartitionKey;
use Dynamite\Configuration\PartitionKeyFormat;

#[Item(objectType: 'U')]
#[PartitionKeyFormat('U')]
class User
{

    #[PartitionKey]
    private string $pk;



    #[NestedItemAttribute(type: IdentityCard::class, name: 'ids', collection: true)]
    public array $identityCards = [];
}