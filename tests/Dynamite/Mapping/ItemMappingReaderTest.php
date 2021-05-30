<?php
declare(strict_types=1);

namespace Dynamite\Mapping;


use Dynamite\Configuration\NestedItem;
use Dynamite\Configuration\NestedItemAttribute;
use Dynamite\Configuration\NestedValueObjectAttribute;
use Dynamite\Fixtures\Dummy;
use Dynamite\Fixtures\DummyItem;
use Dynamite\Fixtures\DummyItemWithInvalidNestedItemReference;
use Dynamite\Fixtures\DummyItemWithInvalidPropNameInNestedVO;
use Dynamite\Fixtures\DummyItemWithMultiplePartitionKeys;
use Dynamite\Fixtures\DummyItemWithMultipleSortKeys;
use Dynamite\Fixtures\DummyItemWithPartitionKeyFormat;
use Dynamite\Fixtures\Valid\ExchangeRate;
use Dynamite\Fixtures\Valid\Php8\AccessToken;
use Dynamite\Fixtures\Valid\Product;
use Dynamite\Fixtures\Valid\UserActivity;
use Dynamite\Test\DynamiteTestSuiteHelperTrait;
use PHPUnit\Framework\TestCase;

class ItemMappingReaderTest extends TestCase
{
    use DynamiteTestSuiteHelperTrait;

    public function testCheckingForMissingItemAnnotation(): void
    {
        $this->expectException(ItemMappingException::class);
        $this->expectExceptionMessage('Class "Dynamite\Fixtures\Dummy" does not have "Dynamite\Configuration\Item" annotation given.');

        $parser = $this->createItemMappingReader();
        $parser->getMappingFor(Dummy::class);
    }

    public function testCheckingForMissingPartitionKeyFormatAnnotation(): void
    {
        $this->expectException(ItemMappingException::class);
        $this->expectExceptionMessage('There is no PartitionKeyFormat annotation set in any property of "Dynamite\Fixtures\DummyItem" class.');

        $parser = $this->createItemMappingReader();
        $parser->getMappingFor(DummyItem::class);
    }

    public function testCheckingForMissingPartitionKeyProp(): void
    {
        $this->expectException(ItemMappingException::class);
        $this->expectExceptionMessage('There is no PartitionKey annotation set in any property of "Dynamite\Fixtures\DummyItemWithPartitionKeyFormat" class.');

        $parser = $this->createItemMappingReader();
        $parser->getMappingFor(DummyItemWithPartitionKeyFormat::class);
    }


    public function testReadingFullPrimaryKeyMapping(): void
    {
        $parser = $this->createItemMappingReader();
        $mapping = $parser->getMappingFor(UserActivity::class);

        $this->assertEquals('USERACTIVITY#{userId}', $mapping->getPartitionKeyFormat());
        $this->assertEquals('pk', $mapping->getPartitionKeyProperty());

        $this->assertEquals('ACT#{activityId}', $mapping->getSortKeyFormat());
        $this->assertEquals('sk', $mapping->getSortKeyProperty());
    }

    public function testReadingObjectType(): void
    {
        $parser = $this->createItemMappingReader();
        $mapping = $parser->getMappingFor(UserActivity::class);

        $this->assertEquals('USERACTIVITY', $mapping->getObjectType());
    }

    public function testClassAnnotationsDefinedAsPhp8Attributes()
    {
        if (PHP_VERSION_ID < 80000) {
            self::markTestSkipped('PHP8 is required to test this thing!');
        }

        $parser = $this->createItemMappingReader();
        $mapping = $parser->getMappingFor(AccessToken::class);

        self::assertEquals('acstkn', $mapping->getObjectType());
        self::assertEquals('O2ACCTKN#{id}', $mapping->getPartitionKeyFormat());

    }


    public function testPlainAttributeAnnotationsDefinedAsPhp8Attributes()
    {
        if (PHP_VERSION_ID < 80000) {
            self::markTestSkipped('PHP8 is required to test this thing!');
        }

        $parser = $this->createItemMappingReader();
        $mapping = $parser->getMappingFor(AccessToken::class);

        self::assertEquals('identifier', $mapping->getProperty('id')->getName());
    }


}