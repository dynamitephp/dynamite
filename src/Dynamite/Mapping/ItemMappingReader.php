<?php
declare(strict_types=1);

namespace Dynamite\Mapping;

use Dynamite\Configuration\Attribute;
use Dynamite\Configuration\AttributeInterface;
use Dynamite\Configuration\DuplicateTo;
use Dynamite\Configuration\Item;
use Dynamite\Configuration\NestedItem;
use Dynamite\Configuration\NestedItemAttribute;
use Dynamite\Configuration\NestedValueObjectAttribute;
use Dynamite\Configuration\PartitionKey;
use Dynamite\Configuration\PartitionKeyFormat;
use Dynamite\Configuration\Shorten;
use Dynamite\Configuration\SortKey;
use Dynamite\Configuration\SortKeyFormat;
use ReflectionClass;
use function reset;

/**
 * @author pizzaminded <mikolajczajkowsky@gmail.com>
 * @license MIT
 */
class ItemMappingReader
{

    /**
     * @param string $className
     * @psalm-param class-string $className
     * @return ItemMapping
     * @throws \ReflectionException
     */
    public function getMappingFor(string $className): ItemMapping
    {
        $classReflection = new ReflectionClass($className);

        $item = null;
        $itemAttrs = $classReflection->getAttributes(Item::class);
        if (count($itemAttrs) > 0) {
            $item = reset($itemAttrs)->newInstance();
        }

        if ($item === null) {
            throw ItemMappingException::notSupported($className);
        }

        /** @var PartitionKeyFormat|null $partitionKeyFormat */
        $partitionKeyFormat = null;
        $pkAttrs = $classReflection->getAttributes(PartitionKeyFormat::class);
        if (count($pkAttrs) > 0) {
            $partitionKeyFormat = reset($pkAttrs)->newInstance();
        }

        if ($partitionKeyFormat === null) {
            throw ItemMappingException::noPartitionKeyFormatFound($className);
        }


        $sortKeyFormat = null;
        /** @var SortKeyFormat|null $sortKeyAnnotation */
        $skfAttrs = $classReflection->getAttributes(SortKeyFormat::class);
        if (count($skfAttrs) > 0) {
            $sortKeyAnnotation = reset($skfAttrs)->newInstance();
        }

        if ($sortKeyAnnotation !== null) {
            $sortKeyFormat = $sortKeyAnnotation->getValue();
        }

        /**
         * Keys => PHP Properties
         * Values => Attribute class
         */
        $attributesMapping = [];
        $shorteners = [];
        $nestedItems = [];
        $partitionKeyAttr = null;
        $sortKeyAttr = null;

        $classPropertyReflections = $classReflection->getProperties();

        $duplicates = $this->findAllDuplicateToAttributes($classReflection);


        foreach ($classPropertyReflections as $propertyReflection) {
            $propertyName = $propertyReflection->getName();

            $attribute = $this->findAttributeType($propertyReflection, $php8);

            if ($attribute !== null) {
                $attributesMapping[$propertyName] = $attribute;

                if ($attribute instanceof NestedItemAttribute) {
                    $nestedItemReflection = new ReflectionClass($attribute->getType());
                    /** @var NestedItem|null $nestedItemConfiguration */
                    $nestedItemConfiguration = $this->reader->getClassAnnotation($nestedItemReflection, NestedItem::class);
                    if ($nestedItemConfiguration === null) {
                        throw ItemMappingException::missingNestemItemAnnotationOnReferencedObject($attribute->getType());
                    }

                    $nestedItems[$propertyName] = $nestedItemConfiguration;
                }

                $shortenerReflections = $propertyReflection->getAttributes(Shorten::class);
                foreach ($shortenerReflections as $shortenerReflection) {
                    $shortener = $shortenerReflection->newInstance();
                    $shorteners[$propertyName][] = $shortener;
                }


                /**
                 * continue to next parameter, as property with any Attribute annotation
                 * cannot have nor partition key or sort key defined
                 */
                continue;
            }

            /** @var PartitionKey|null $partitionKey */
            $partitionKey = $this->reader->getPropertyAnnotation($propertyReflection, PartitionKey::class);

            if ($partitionKey === null && $php8) {
                $pkAttrs = $propertyReflection->getAttributes(PartitionKey::class);
                if (count($pkAttrs) > 0) {
                    $partitionKey = reset($pkAttrs)->newInstance();
                }
            }
            if ($partitionKey !== null && $partitionKeyAttr !== null) {
                throw ItemMappingException::moreThanOnePartitionKey($partitionKeyAttr, $propertyName, $className);
            }

            if ($partitionKey !== null && $partitionKeyAttr === null) {
                $partitionKeyAttr = $propertyName;
            }

            /** @var SortKey|null $sortKey */
            $sortKey = $this->reader->getPropertyAnnotation($propertyReflection, SortKey::class);

            if ($sortKey === null && $php8) {
                $skAttrs = $propertyReflection->getAttributes(SortKey::class);
                if (count($skAttrs) > 0) {
                    $sortKey = reset($skAttrs)->newInstance();
                }
            }
            if ($sortKeyAttr !== null && $sortKey !== null) {
                throw ItemMappingException::moreThanOneSortKey($sortKeyAttr, $propertyName, $className);
            }
            if ($sortKeyAttr === null && $sortKey !== null) {
                $sortKeyAttr = $propertyName;
            }
        }

        // @TODO: PartionKey should be optional, i have no idea when this should be useful
        if ($partitionKeyAttr === null) {
            throw ItemMappingException::noPartitionKeyFound($className);
        }

        return new ItemMapping(
            $item,
            new Key($partitionKeyFormat->getValue(), $partitionKeyAttr),
            $attributesMapping,
            ($sortKeyAttr !== null && $sortKeyFormat !== null ? new Key($sortKeyFormat, $sortKeyAttr) : null),
            $nestedItems,
            $duplicates,
            $shorteners
        );

    }

    /**
     * @param \ReflectionProperty $property
     * @return AttributeInterface|null
     */
    protected function findAttributeType(\ReflectionProperty $property, bool $php8): ?AttributeInterface
    {
        /** @var null|Attribute $simpleAttribute */
        $simpleAttribute = $this->reader->getPropertyAnnotation($property, Attribute::class);

        if ($simpleAttribute === null && $php8) {
            $simpleAttrs = $property->getAttributes(Attribute::class);
            if (count($simpleAttrs) > 0) {
                return reset($simpleAttrs)->newInstance();
            }
        }

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
                throw ItemMappingException::noPropertyInClass($propertyToCheck, $nestedValueObject->getType());
            }
        }

        return $nestedValueObject;
    }


    private function findAllDuplicateToAttributes(ReflectionClass $reflectionClass): array {
        $attrs = $reflectionClass->getAttributes();
        $output = [];

        foreach ($attrs as $attr) {
            if($attr->getName() === DuplicateTo::class) {
                $output[] = $attr->newInstance();
            }
        }
        
        return $output;
    }
}