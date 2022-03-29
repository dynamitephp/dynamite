<?php
declare(strict_types=1);

namespace Dynamite\Mapping;


use Dynamite\Fixtures\Dummy;
use Dynamite\Fixtures\DummyItem;
use Dynamite\Fixtures\DummyItemWithPartitionKeyFormat;
use Dynamite\Fixtures\Valid\Php8\AccessToken;
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

        self::assertEquals('USERACTIVITY#{userId}', $mapping->getPartitionKeyFormat());
        self::assertEquals('pk', $mapping->getPartitionKeyProperty());

        self::assertEquals('ACT#{activityId}', $mapping->getSortKeyFormat());
        self::assertEquals('sk', $mapping->getSortKeyProperty());
    }

    public function testReadingObjectType(): void
    {
        $parser = $this->createItemMappingReader();
        $mapping = $parser->getMappingFor(UserActivity::class);

        self::assertEquals('USERACTIVITY', $mapping->getObjectType());
    }

    public function testClassAnnotationsDefinedAsPhp8Attributes(): void
    {
        $parser = $this->createItemMappingReader();
        $mapping = $parser->getMappingFor(AccessToken::class);

        self::assertEquals('acstkn', $mapping->getObjectType());
        self::assertEquals('O2ACCTKN#{id}', $mapping->getPartitionKeyFormat());

    }


    public function testPlainAttributeAnnotationsDefinedAsPhp8Attributes(): void
    {
        $parser = $this->createItemMappingReader();
        $mapping = $parser->getMappingFor(AccessToken::class);

        self::assertEquals('identifier', $mapping->getProperty('id')->getName());
    }


}