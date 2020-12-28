<?php
declare(strict_types=1);

namespace Dynamite\Test;


use Doctrine\Common\Annotations\AnnotationReader;
use Dynamite\Mapping\ItemMappingReader;

trait  DynamiteTestSuiteHelperTrait
{

    private function createItemMappingReader(): ItemMappingReader
    {
        return new ItemMappingReader(new AnnotationReader());
    }
}