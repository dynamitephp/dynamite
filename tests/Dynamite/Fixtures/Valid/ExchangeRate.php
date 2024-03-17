<?php
declare(strict_types=1);

namespace Dynamite\Fixtures\Valid;

use Dynamite\Configuration as Dynamite;

#[Dynamite\Item(objectType: "EXCHANGERATE")]
#[Dynamite\PartitionKeyFormat("EXCHANGERATE#{date}")]
#[Dynamite\SortKeyFormat("RATE#{from}#{to}")]
class ExchangeRate
{
    #[Dynamite\PartitionKey()]
    private string $pk;

    #[Dynamite\SortKey()]
    private string $sk;

    #[Dynamite\NestedValueObjectAttribute(
        type: CurrencyNestedValueObject::class,
        name: "fr",
        property: "value"
    )]
    private CurrencyNestedValueObject $from;

    #[Dynamite\Attribute(
        name: "dt",
        type: "string"
    )]
    private $date;

    #[Dynamite\NestedValueObjectAttribute(
        type: CurrencyNestedValueObject::class,
        name: "to",
        property: "value"
    )]
    private CurrencyNestedValueObject $to;

    private float $value;

    public function __construct(CurrencyNestedValueObject $from, CurrencyNestedValueObject $to)
    {
        $this->from = $from;
        $this->to = $to;
    }

}