<?php
declare(strict_types=1);

namespace Dynamite;


use Dynamite\Exception\SerializationException;
use Dynamite\Fixtures\IdentityCard;
use Dynamite\Fixtures\User;
use Dynamite\Fixtures\Valid\BankAccount;
use Dynamite\Fixtures\Valid\Car;
use Dynamite\Fixtures\Valid\CurrencyNestedValueObject;
use Dynamite\Fixtures\Valid\ExchangeRate;
use Dynamite\Fixtures\Valid\Product;
use Dynamite\Fixtures\Valid\ProductNutritionNestedItem;
use Dynamite\Fixtures\Valid\UserActivity;
use Dynamite\Test\DynamiteTestSuiteHelperTrait;
use PHPUnit\Framework\TestCase;

class ItemSerializerTest extends TestCase
{
    use DynamiteTestSuiteHelperTrait;

    public function testObjectSerializationWithNestedItemWithCustomSerializer(): void
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

        self::assertSame($snapshot, $serializedProduct);
    }

    public function testObjectSerializationWithNestedItemCollectionWithCustomSerializer(): void
    {
        $serializer = $this->createItemSerializer();
        $mappingReader = $this->createItemMappingReader();

        $user = new User();

        $user->identityCards[] = new IdentityCard('PASSPORT', 'XXX123');
        $user->identityCards[] = new IdentityCard('GOV_ID', 'YYY123');

        $productMapping = $mappingReader->getMappingFor(User::class);
        $serializedProduct = $serializer->serialize($user, $productMapping);

        $snapshot = [
            'ids' => [
                [
                    'type' => 'PASSPORT',
                    'number' => 'XXX123'
                ],
                [
                    'type' => 'GOV_ID',
                    'number' => 'YYY123'
                ]
            ],

        ];

        self::assertSame($snapshot, $serializedProduct);
    }


    public function testObjectSerializationForNestedValueObject(): void
    {
        $serializer = $this->createItemSerializer();
        $mappingReader = $this->createItemMappingReader();

        $mapping = $mappingReader->getMappingFor(ExchangeRate::class);

        $exchange = new ExchangeRate(new CurrencyNestedValueObject('CZK'), new CurrencyNestedValueObject('PLN'));

        $serialized = $serializer->serialize($exchange, $mapping);

        $snapshot = [
            'fr' => 'CZK',
            'dt' => null,
            'to' => 'PLN'
        ];


        self::assertSame($snapshot, $serialized);
    }

    public function testObjectSerializationForNestedValueObjectCollection(): void
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

        self::assertSame($snapshot, $serialized);
    }

    public function testShorteningSerialization(): void
    {
        $serializer = $this->createItemSerializer();
        $mappingReader = $this->createItemMappingReader();

        $mapping = $mappingReader->getMappingFor(Car::class);

        $car = new Car();
        $car->id = '123456789';
        $car->firstRegistrationCountry = 'France';
        $serialized = $serializer->serialize($car, $mapping);

        self::assertSame('FR', $serialized['frc']);
    }

    public function testShorteningSerializationButAnyShortenerMatches(): void
    {
        $serializer = $this->createItemSerializer();
        $mappingReader = $this->createItemMappingReader();

        $mapping = $mappingReader->getMappingFor(Car::class);

        $car = new Car();
        $car->id = '123456789';
        $car->firstRegistrationCountry = 'Poland';
        $serialized = $serializer->serialize($car, $mapping);

        self::assertSame('Poland', $serialized['frc']);
    }

    public function testShorteningHydration(): void
    {
        $serializer = $this->createItemSerializer();
        $mappingReader = $this->createItemMappingReader();

        $mapping = $mappingReader->getMappingFor(Car::class);


        $data = [
            'id' => '420',
            'frc' => 'IT'
        ];

        /** @var Car $deserialized */
        $deserialized = $serializer->hydrateObject(Car::class, $mapping, $data);

        self::assertSame('Italy', $deserialized->firstRegistrationCountry);
    }

    public function testShorteningHydrationButNothingMatches(): void
    {
        $serializer = $this->createItemSerializer();
        $mappingReader = $this->createItemMappingReader();

        $mapping = $mappingReader->getMappingFor(Car::class);


        $data = [
            'id' => '666',
            'frc' => 'WTF'
        ];

        /** @var Car $deserialized */
        $deserialized = $serializer->hydrateObject(Car::class, $mapping, $data);

        self::assertSame('WTF', $deserialized->firstRegistrationCountry);
    }

    public function testObjectHydrationWithNestedValueObjectCollection(): void
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
        $deserialized = $serializer->hydrateObject(BankAccount::class, $mapping, $data);

        self::assertInstanceOf(BankAccount::class, $deserialized);
        self::assertCount(3, $deserialized->getSupportedCurrencies());

        $vo = $deserialized->getSupportedCurrencies()[0];
        self::assertInstanceOf(CurrencyNestedValueObject::class, $vo);
        self::assertSame('EUR', $vo->getValue());
    }

    public function testExceptionWillBeThrownWhenSerializerWillAttemptToInjectNullIntoNotNullableProp()
    {
        $serializer = $this->createItemSerializer();
        $mappingReader = $this->createItemMappingReader();

        $mapping = $mappingReader->getMappingFor(UserActivity::class);

        $data = [
            'userId' => '5',
            'activityId' => null
        ];

        $this->expectException(SerializationException::class);
        $this->expectExceptionMessage('Cannot create an item "Dynamite\Fixtures\Valid\UserActivity", as typed property "activityId" is not null, but there is no value from DB');

        $serializer->hydrateObject(UserActivity::class, $mapping, $data);
    }

    public function testExceptionWillBeThrownWhenSerializerWillAttemptToInjectMissingValueIntoNotNullableProp()
    {
        $serializer = $this->createItemSerializer();
        $mappingReader = $this->createItemMappingReader();

        $mapping = $mappingReader->getMappingFor(UserActivity::class);

        $data = [
           'userId' => '5'
        ];

        $this->expectException(SerializationException::class);
        $this->expectExceptionMessage('Cannot create an item "Dynamite\Fixtures\Valid\UserActivity", as typed property "activityId" is not null, but there is no value from DB');

        $serializer->hydrateObject(UserActivity::class, $mapping, $data);
    }
}