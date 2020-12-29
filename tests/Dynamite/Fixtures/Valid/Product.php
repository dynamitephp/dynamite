<?php
declare(strict_types=1);

namespace Dynamite\Fixtures\Valid;
use Dynamite\Configuration as Dynamite;

/**
 * @Dynamite\Item(objectType="PRODUCT")
 * @Dynamite\PartitionKeyFormat("PRODUCT#{ean}")
 * @Dynamite\SortKeyFormat("PRODUCT")
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

    /**
     * @Dynamite\Attribute(type="string", name="ean")
     * @var string
     */
    private string $ean;

    /**
     * @Dynamite\Attribute(type="string", name="name")
     * @var string
     */
    private string $name;

    /**
     * @Dynamite\Attribute(type="number", name="wght")
     * @var string
     */
    private int $weight;

    /**
     * @return ProductNutritionNestedItem
     */
    public function getNutritionFacts(): ProductNutritionNestedItem
    {
        return $this->nutritionFacts;
    }

    /**
     * @param ProductNutritionNestedItem $nutritionFacts
     * @return Product
     */
    public function setNutritionFacts(ProductNutritionNestedItem $nutritionFacts): Product
    {
        $this->nutritionFacts = $nutritionFacts;
        return $this;
    }

    /**
     * @return string
     */
    public function getEan(): string
    {
        return $this->ean;
    }

    /**
     * @param string $ean
     * @return Product
     */
    public function setEan(string $ean): Product
    {
        $this->ean = $ean;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Product
     */
    public function setName(string $name): Product
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return int
     */
    public function getWeight(): int
    {
        return $this->weight;
    }

    /**
     * @param int $weight
     * @return Product
     */
    public function setWeight(int $weight): Product
    {
        $this->weight = $weight;
        return $this;
    }


}