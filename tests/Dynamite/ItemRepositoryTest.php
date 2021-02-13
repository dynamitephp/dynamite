<?php


namespace Dynamite;

use Dynamite\Exception\ItemNotFoundException;
use Dynamite\Fixtures\Valid\ExchangeRate;
use Dynamite\Fixtures\Valid\Product;
use Dynamite\Test\DynamiteTestSuiteHelperTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ItemRepositoryTest extends TestCase
{

    use DynamiteTestSuiteHelperTrait;

    public function testGetItemWillBreakWhenSingleTableServiceReturnNothing()
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
            $this->createItemSerializer()
        );


        $this->expectException(ItemNotFoundException::class);
        $this->expectExceptionMessage('Could not find item with PK "123" and SK "PRODUCT"');
        $itemRepository->getItem('123');
    }

    public function testGetItemIsProperlyBuildingPartitionKeyFromArrayOfPropsAndDefinedFormat()
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
            $this->createItemSerializer()
        );


        $object = $itemRepository->getItem(
            ['date' => '2021-02-13'],
            ['from' => 'CZK', 'to' => 'PLN']
        );

        $this->assertInstanceOf(ExchangeRate::class, $object);
    }
}