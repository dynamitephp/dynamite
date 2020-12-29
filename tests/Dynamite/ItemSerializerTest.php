<?php
declare(strict_types=1);

namespace Dynamite;


use Dynamite\Fixtures\Valid\Product;
use Dynamite\Fixtures\Valid\ProductNutritionNestedItem;
use Dynamite\Test\DynamiteTestSuiteHelperTrait;
use PHPUnit\Framework\TestCase;

class ItemSerializerTest extends TestCase
{
    use DynamiteTestSuiteHelperTrait;

    public function testObjectSerializationWithNestedItemWithCustomSerializer()
    {
        $serializer = $this->createItemSerializer();
        $mappingReader = $this->createItemMappingReader();


        $product = new Product();
        $product
            ->setName('Tuna')
            ->setWeight(350)
            ->setEan('59012345678')
            ->setNutritionFacts(
                (new ProductNutritionNestedItem())
                ->setCarbs(1.23)
                ->setCalories(400)
                ->setAllergens(['cardboard'])
            );

        $productMapping = $mappingReader->getMappingFor(Product::class);

        $serializedProduct = $serializer->serialize($product, $productMapping);

        $snapshot = [
            'nf' => [
                'algs' => ['cardboard'],
                'carb' => 1.23,
                'kcal' => 400
            ],
            'ean' => '59012345678',
            'name' => 'Tuna',
            'wght' => 350
        ];

        $this->assertSame($snapshot, $serializedProduct);
    }

}