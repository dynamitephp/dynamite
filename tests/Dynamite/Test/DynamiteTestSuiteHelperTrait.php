<?php
declare(strict_types=1);

namespace Dynamite\Test;


use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\Reader;
use Dynamite\ItemSerializer;
use Dynamite\Mapping\ItemMappingReader;
use Dynamite\PrimaryKey\KeyFormatResolver;
use Dynamite\TableSchema;

trait  DynamiteTestSuiteHelperTrait
{
    private function createItemMappingReader(): ItemMappingReader
    {
        return new ItemMappingReader();
    }

    private function createItemSerializer(): ItemSerializer
    {
        return new ItemSerializer();
    }


    private function createKeyFormatResolver(): KeyFormatResolver
    {
        return new KeyFormatResolver();
    }

    private function getGenericTableConfiguration()
    {
        return new TableSchema('dynamitetest');
    }

}