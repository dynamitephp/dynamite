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

    private ?Reader $_reader = null;

    private function createItemMappingReader(): ItemMappingReader
    {
        return new ItemMappingReader($this->getAnnotationReader());
    }


    private function getAnnotationReader(): Reader
    {
        if ($this->_reader === null) {
            $this->_reader = new AnnotationReader();
        }

        return $this->_reader;
    }

    private function createItemSerializer(): ItemSerializer
    {
        return new ItemSerializer($this->getAnnotationReader());
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