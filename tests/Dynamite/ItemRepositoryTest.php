<?php


namespace Dynamite;

use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Marshaler;
use Aws\MockHandler;
use Dynamite\Exception\ItemNotFoundException;
use Dynamite\Fixtures\Valid\ExchangeRate;
use Dynamite\Fixtures\Valid\Product;
use Dynamite\Fixtures\Valid\User;
use Dynamite\PrimaryKey\KeyFormatResolver;
use Dynamite\Test\DynamiteTestSuiteHelperTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class ItemRepositoryTest extends TestCase
{

    use DynamiteTestSuiteHelperTrait;

    public function testGetItemWillBreakWhenSingleTableServiceReturnNothing(): void
    {
        /** @var MockObject|SingleTableService $stsMock */
        $stsMock = $this->getMockBuilder(SingleTableService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $stsMock
            ->method('getItem')
            ->willReturn(null);


        $itemRepository = new ItemRepository(
            $stsMock,
            Product::class,
            $this->createItemMappingReader()->getMappingFor(Product::class),
            $this->createItemSerializer(),
            $this->createKeyFormatResolver()
        );


        $this->expectException(ItemNotFoundException::class);
        $this->expectExceptionMessage('Could not find item with PK "123" and SK "PRODUCT"');
        $itemRepository->getItem('123');
    }

    public function testGetItemIsProperlyBuildingPartitionKeyFromArrayOfPropsAndDefinedFormat(): void
    {
        /** @var MockObject|SingleTableService $stsMock */
        $stsMock = $this->getMockBuilder(SingleTableService::class)
            ->disableOriginalConstructor()
            ->getMock();


        $snapshot = [
            'fr' => 'CZK',
            'to' => 'PLN'
        ];

        $stsMock
            ->method('getItem')
            ->willReturnMap(
                [
                    ['EXCHANGERATE#2021-02-13', 'RATE#CZK#PLN', $snapshot]
                ]
            );


        $itemRepository = new ItemRepository(
            $stsMock,
            ExchangeRate::class,
            $this->createItemMappingReader()
                ->getMappingFor(ExchangeRate::class),
            $this->createItemSerializer(),
            $this->createKeyFormatResolver()
        );


        $object = $itemRepository->getItem(
            ['date' => '2021-02-13'],
            ['from' => 'CZK', 'to' => 'PLN']
        );

        self::assertInstanceOf(ExchangeRate::class, $object);
    }


    public function testGetItemIsProperlyPuttingItemWithDuplicates(): void
    {

        $mockHandler = new MockHandler();
        $ddbMock = new DynamoDbClient([
            'handler' => $mockHandler,
            'version' => '2012-08-10',
            'region' => 'eu-central-1'
        ]);

        /** @var MockObject|SingleTableService $stsMock */
        $stsMock = $this->getMockBuilder(SingleTableService::class)
            ->setConstructorArgs([
                $ddbMock,
                $this->getGenericTableConfiguration(),
                new Marshaler(),
                new NullLogger()
            ])

            ->enableOriginalConstructor()
            ->enableProxyingToOriginalMethods()
            ->getMock();

        $stsMock
            ->method('writeRequestBatch')
            ->willReturnCallback(function (array $put, array $delete) {
                $snapshot = [
                    [
                        'id' => '1',
                        'mail' => 'mickey@example.com',
                        'nick' => 'mickey',
                        'dnam' => 'george',
                        'objectType' => 'USER',
                        'pk' => 'USER#1',
                        'sk' => 'USER'
                    ],
                    [
                        'id' => '1',
                        'pk' => 'UDATA#mickey@example.com',
                        'sk' => 'UDATA'
                    ],
                    [
                        'id' => '1',
                        'pk' => 'UDATA#mickey',
                        'sk' => 'UDATA'
                    ]
                ];
                $this->assertEmpty($delete);
                $this->assertSame($snapshot, $put);

            });

        $itemRepository = new ItemRepository(
            $stsMock,
            User::class,
            $this->createItemMappingReader()
                ->getMappingFor(User::class),
            $this->createItemSerializer(),
            $this->createKeyFormatResolver()

        );


        $user = new User('1', 'mickey@example.com', 'mickey', 'george');
        $itemRepository->put($user);
    }
}