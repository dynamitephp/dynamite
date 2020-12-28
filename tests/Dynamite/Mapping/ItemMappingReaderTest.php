<?php
declare(strict_types=1);

namespace Dynamite\Mapping;


use Doctrine\Common\Annotations\AnnotationReader;
use Dynamite\Fixtures\Dummy;
use Dynamite\Fixtures\DummyItem;
use Dynamite\Fixtures\DummyItemWithPartitionKeyFormat;
use Dynamite\Fixtures\Valid\UserActivity;
use PHPUnit\Framework\TestCase;

class ItemMappingReaderTest extends TestCase
{

    private function createItemMappingReader(): ItemMappingReader
    {
        return new ItemMappingReader(new AnnotationReader());
    }


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
}