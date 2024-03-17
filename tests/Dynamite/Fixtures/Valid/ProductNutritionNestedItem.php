<?php
declare(strict_types=1);

namespace Dynamite\Fixtures\Valid;
use Dynamite\Configuration as Dynamite;

#[Dynamite\NestedItem(serializeMethod:"toArray")]
class ProductNutritionNestedItem
{
    private int $calories;

    private float $carbs;

    private array $allergens;

    /**
     * @return int
     */
    public function getCalories(): int
    {
        return $this->calories;
    }

    /**
     * @param int $calories
     * @return ProductNutritionNestedItem
     */
    public function setCalories(int $calories): ProductNutritionNestedItem
    {
        $this->calories = $calories;
        return $this;
    }

    /**
     * @return float
     */
    public function getCarbs(): float
    {
        return $this->carbs;
    }

    /**
     * @param float $carbs
     * @return ProductNutritionNestedItem
     */
    public function setCarbs(float $carbs): ProductNutritionNestedItem
    {
        $this->carbs = $carbs;
        return $this;
    }

    /**
     * @return array
     */
    public function getAllergens(): array
    {
        return $this->allergens;
    }

    /**
     * @param array $allergens
     * @return ProductNutritionNestedItem
     */
    public function setAllergens(array $allergens): ProductNutritionNestedItem
    {
        $this->allergens = $allergens;
        return $this;
    }

    public function toArray()
    {
        return [
            'algs' => $this->allergens,
            'carb' => $this->carbs,
            'kcal' => $this->calories
        ];
    }
}