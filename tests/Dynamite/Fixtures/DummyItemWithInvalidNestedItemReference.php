<?php
declare(strict_types=1);

namespace Dynamite\Fixtures;

use Dynamite\Configuration as Dynamite;

/**
 * @Dynamite\Item(objectType="D00MMY")
 * @Dynamite\PartitionKeyFormat("D00MY")
 * @Dynamite\SortKeyFormat("D")
 */
class DummyItemWithInvalidNestedItemReference
{

    /**
     * @Dynamite\NestedItemAttribute(type="\Dynamite\Fixtures\NestedItemWithoutAnnotation", name="prop")
     * @var NestedItemWithoutAnnotation
     */
    private NestedItemWithoutAnnotation $prop;
}