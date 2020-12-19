<?php
declare(strict_types=1);

namespace Dynamite\Mapping;


use Doctrine\Common\Annotations\Reader;
use Dynamite\Configuration\Attribute;
use Dynamite\Configuration\AttributeInterface;
use Dynamite\Configuration\Item;
use Dynamite\Configuration\NestedItemAttribute;
use Dynamite\Configuration\NestedValueObjectAttribute;
use Dynamite\Configuration\PartitionKey;
use Dynamite\Configuration\PartitionKeyFormat;
use Dynamite\Configuration\SortKey;
use Dynamite\Configuration\SortKeyFormat;
use Dynamite\ItemMapping;
use ReflectionClass;

/**
 * @author pizzaminded <mikolajczajkowsky@gmail.com>
 * @license MIT
 */
class ItemMappingReader
{

    protected Reader $reader;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @param string $className
     * @return ItemMapping
     * @throws \ReflectionException
     */
    public function getMappingFor(string $className): ItemMapping
    {
        $classReflection = new ReflectionClass($className);

        /** @var Item $item */
        $item = $this->reader->getClassAnnotation($classReflection, Item::class);
        if ($item === null) {
            throw ItemMappingException::notSupported($className);
        }

        /** @var PartitionKeyFormat|null $partitionKeyFormat */
        $partitionKeyFormat = $this->reader->getClassAnnotation($classReflection, PartitionKeyFormat::class);
        if ($partitionKeyFormat === null) {
            throw ItemMappingException::noPartitionKeyFormatFound($className);
        }


        $sortKeyFormat = null;
        /** @var SortKeyFormat|null $sortKeyAnnotation */
        $sortKeyAnnotation = $this->reader->getClassAnnotation($classReflection, SortKeyFormat::class);
        if ($sortKeyAnnotation !== null) {
            $sortKeyFormat = $sortKeyAnnotation->getValue();
        }

        /**
         * Keys => PHP Properties
         * Values => Attribute class
         */
        $attributesMapping = [];
        $partitionKeyAttr = null;
        $sortKeyAttr = null;

        $classPropertyReflections = $classReflection->getProperties();
        foreach ($classPropertyReflections as $propertyReflection) {
            $propertyName = $propertyReflection->getName();

            $attribute = $this->findAttributeType($propertyReflection);
            if ($attribute !== null) {
                $attributesMapping[$propertyName] = $attribute;
                continue;
            }

            /** @var PartitionKey|null $partitionKey */
            $partitionKey = $this->reader->getPropertyAnnotation($propertyReflection, PartitionKey::class);
            if ($partitionKey !== null && $partitionKeyAttr !== null) {
                throw ItemMappingException::moreThanOnePartitionKey($partitionKeyAttr, $propertyName, $className);
            }

            if ($partitionKey !== null && $partitionKeyAttr === null) {
                $partitionKeyAttr = $propertyName;
            }

            /** @var SortKey|null $sortKey */
            $sortKey = $this->reader->getPropertyAnnotation($propertyReflection, SortKey::class);
            if ($sortKeyAttr !== null && $sortKey !== null) {
                throw ItemMappingException::moreThanOneSortKey($sortKeyAttr, $propertyName, $className);
            }
            if ($sortKeyAttr === null && $sortKey !== null) {
                $sortKeyAttr = $propertyName;
            }
        }

        if ($partitionKeyAttr === null) {
            throw ItemMappingException::noPartitionKeyFound($className);
        }

        return new ItemMapping(
            $item,
            new Key($partitionKeyFormat->getValue(), $partitionKeyAttr),
            $attributesMapping,
            ($sortKeyAttr !== null && $sortKeyFormat !== null ? new Key($sortKeyFormat, $sortKeyAttr) : null)
        );

    }

    /**
     * @param \ReflectionProperty $property
     * @return AttributeInterface|null
     */
    protected function findAttributeType(\ReflectionProperty $property): ?AttributeInterface
    {
        /** @var null|Attribute $simpleAttribute */
        $simpleAttribute = $this->reader->getPropertyAnnotation($property, Attribute::class);

        if ($simpleAttribute !== null) {
            return $simpleAttribute;
        }

        /** @var null|NestedItemAttribute $nestedItemAttribute */
        $nestedItemAttribute = $this->reader->getPropertyAnnotation($property, NestedItemAttribute::class);

        if ($nestedItemAttribute !== null) {
            return $nestedItemAttribute;
        }

        /** @var null|NestedValueObjectAttribute $nestedValueObject */
        $nestedValueObject = $this->reader->getPropertyAnnotation($property, NestedValueObjectAttribute::class);

        if ($nestedValueObject !== null) {
            $nestedValueObjectItemReflection = new ReflectionClass($nestedValueObject->getType());

            $propertyToCheck = $nestedValueObject->getProperty();
            if (!$nestedValueObjectItemReflection->hasProperty($propertyToCheck)) {
                throw new ItemMappingException('There is no "%s" property in "%s" class.', $propertyToCheck, $nestedValueObject->getType());
            }
        }

        return $nestedValueObject;
    }

}