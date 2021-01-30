<?php
declare(strict_types=1);

namespace Dynamite;


use Dynamite\Fixtures\Valid\BankAccount;
use Dynamite\Fixtures\Valid\CurrencyNestedValueObject;
use Dynamite\Fixtures\Valid\ExchangeRate;
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


    public function testObjectSerializationForNestedValueObject()
    {
        $serializer = $this->createItemSerializer();
        $mappingReader = $this->createItemMappingReader();

        $mapping = $mappingReader->getMappingFor(ExchangeRate::class);

        $exchange = new ExchangeRate(new CurrencyNestedValueObject('CZK'), new CurrencyNestedValueObject('PLN'));

        $serialized = $serializer->serialize($exchange, $mapping);

        $snapshot = [
            'fr' => 'CZK',
            'to' => 'PLN'
        ];


        $this->assertSame($snapshot, $serialized);
    }

    public function testObjectSerializationForNestedValueObjectCollection()
    {
        $serializer = $this->createItemSerializer();
        $mappingReader = $this->createItemMappingReader();

        $mapping = $mappingReader->getMappingFor(BankAccount::class);


        $account = new BankAccount();
        $account->addSupportedCurrency(new CurrencyNestedValueObject('EUR'));
        $account->addSupportedCurrency(new CurrencyNestedValueObject('PLN'));
        $account->addSupportedCurrency(new CurrencyNestedValueObject('CHF'));

        $serialized = $serializer->serialize($account, $mapping);

        $snapshot = [
            'cur' => [
                'EUR',
                'PLN',
                'CHF'
            ]
        ];

        $this->assertSame($snapshot, $serialized);
    }

    public function testObjectHydrationWithNestedValueObjectCollection()
    {
        $serializer = $this->createItemSerializer();
        $mappingReader = $this->createItemMappingReader();

        $mapping = $mappingReader->getMappingFor(BankAccount::class);


        $data = [
            'cur' => [
                'EUR',
                'PLN',
                'CHF'
            ]
        ];


        /** @var BankAccount $deserialized */
        $deserialized = $serializer->hydrateObject(BankAccount::class, $mapping,$data );

        $this->assertInstanceOf(BankAccount::class, $deserialized);
        $this->assertCount(3, $deserialized->getSupportedCurrencies());

        $vo = $deserialized->getSupportedCurrencies()[0];
        $this->assertInstanceOf(CurrencyNestedValueObject::class, $vo);
        $this->assertSame('EUR', $vo->getValue());
    }
}