<?php
declare(strict_types=1);

namespace Dynamite\Fixtures\Valid;
use Dynamite\Configuration as Dynamite;

/**
 * @Dynamite\Item(objectType="EXCHANGERATE")
 * @Dynamite\PartitionKeyFormat("EXCHANGERATE#{date}")
 * @Dynamite\SortKeyFormat("RATE#{from}#{to}")
 */
class ExchangeRate
{

    /**
     * @Dynamite\PartitionKey()
     * @var string
     */
    private string $pk;

    /**
     * @Dynamite\SortKey()
     * @var string
     */
    private string $sk;

    /**
     * @Dynamite\NestedValueObjectAttribute(name="fr", type="\Dynamite\Fixtures\Valid\CurrencyNestedValueObject", property="value")
     * @var CurrencyNestedValueObject
     */
    private CurrencyNestedValueObject $from;

    /**
     * @Dynamite\NestedValueObjectAttribute(name="to", type="\Dynamite\Fixtures\Valid\CurrencyNestedValueObject", property="value")
     * @var CurrencyNestedValueObject
     */
    private CurrencyNestedValueObject $to;

    /**
     * @Dynamite\NestedValueObjectAttribute(name="date", type="\Dynamite\Fixtures\Valid\CurrencyNestedValueObject", property="value")
     * @var CurrencyNestedValueObject
     */
    private \DateTimeInterface $date;

    private float $value;

}