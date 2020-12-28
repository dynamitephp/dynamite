<?php
declare(strict_types=1);

namespace Dynamite\Fixtures;

use Dynamite\Configuration as Dynamite;
use Dynamite\Fixtures\Valid\CurrencyNestedValueObject;

/**
 * @Dynamite\Item(objectType="DUMMY")
 * @Dynamite\PartitionKeyFormat("DUMMY")
 * @Dynamite\SortKeyFormat("DUMMY}")
 */
class DummyItemWithInvalidPropNameInNestedVO
{

    /**
     * @Dynamite\NestedValueObjectAttribute(name="date", type="\Dynamite\Fixtures\Valid\CurrencyNestedValueObject", property="weather")
     * @var CurrencyNestedValueObject
     */
    private \DateTimeInterface $date;
}