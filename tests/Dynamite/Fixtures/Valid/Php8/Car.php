<?php


namespace Dynamite\Fixtures\Valid\Php8;

use Dynamite\Configuration as Dynamite;

#[Dynamite\Item(objectType: 'acstkn')]
#[Dynamite\PartitionKeyFormat('O2ACCTKN#{id}')]
#[Dynamite\SortKeyFormat('O2ACCTKN')]
class Car
{

    #[Dynamite\PartitionKey]
    protected $pk;

    #[Dynamite\Attribute('id', type: 'string')]
    public string $id;

    #[Dynamite\Attribute('frc', type: 'string')]
    #[Dynamite\Shorten(from: 'France', to: 'FR')]
    #[Dynamite\Shorten(from: 'Germany', to: 'DE')]
    #[Dynamite\Shorten(from: 'Italy', to: 'IT')]
    public string $firstRegistrationCountry;

}