<?php
declare(strict_types=1);

namespace Dynamite\Mapping;


use Dynamite\Configuration\Item;
use Dynamite\Exception\DynamiteException;

/**
 * @author pizzaminded <mikolajczajkowsky@gmail.com>
 * @license MIT
 */
class ItemMappingException extends DynamiteException
{
    public static function notSupported(string $className): self
    {
        return new self(sprintf('Class "%s" does not have "%s" annotation given.', $className, Item::class));
    }

    public static function moreThanOnePartitionKey(string $first, string $second, string $className): self
    {
        return new self(sprintf('Found two PartitionKey annotations (properties: "%s", "%s") in "%s" class.', $first, $second, $className));
    }

    public static function moreThanOneSortKey(string $first, string $second, string $className): self
    {
        return new self(sprintf('Found two SortKey annotations (properties: "%s", "%s") in "%s" class.', $first, $second, $className));
    }

    public static function noPartitionKeyFound(string $className): self
    {
        return new self(sprintf('There is no PartitionKey annotation set in any property of "%s" class.', $className));
    }

    public static function noPartitionKeyFormatFound(string $className): self
    {
        return new self(sprintf('There is no PartitionKeyFormat annotation set in any property of "%s" class.', $className));
    }

    public static function noPropertyInClass(string $propName, string $fqcn): self
    {
        return new self(sprintf('There is no "%s" property in "%s" class.', $propName, $fqcn));
    }

    public static function missingNestemItemAnnotationOnReferencedObject(string $fqcn)
    {
        return new self(sprintf('There is no NestedItem annotation on "%s" class.', $fqcn));
    }

}