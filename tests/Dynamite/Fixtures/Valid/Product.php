<?php
declare(strict_types=1);

namespace Dynamite\Fixtures\Valid;
use Dynamite\Configuration as Dynamite;

/**
 * @Dynamite\Item(objectType="EXCHANGERATE")
 * @Dynamite\PartitionKeyFormat("EXCHANGERATE#{date}")
 * @Dynamite\SortKeyFormat("RATE#{from}#{to}")
 */
class Product
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
     * @Dynamite\NestedItemAttribute(name="nf", type="\Dynamite\Fixtures\Valid\ProductNutritionNestedItem")
     * @var ProductNutritionNestedItem
     */
    private ProductNutritionNestedItem $nutritionFacts;
}