<?php
declare(strict_types=1);

namespace Dynamite;

use Dynamite\Configuration\Attribute;
use Dynamite\Configuration\NestedItemAttribute;
use Dynamite\Configuration\NestedValueObjectAttribute;
use Dynamite\Exception\DynamiteException;
use Dynamite\Mapping\ItemMapping;
use ReflectionClass;


/**
 * @author pizzaminded <mikolajczajkowsky@gmail.com>
 * @license MIT
 */
class ItemSerializer
{
    public function serialize(object $item, ItemMapping $itemMapping): array
    {
        $values = [];
        $reflectionClass = new ReflectionClass($item);
        foreach ($itemMapping->getPropertiesMapping() as $propertyName => $attribute) {
            $attrName = $attribute->getName();
            $propertyReflection = $reflectionClass->getProperty($propertyName);
            $propertyReflection->setAccessible(true);
            $propertyValue = $propertyReflection->getValue($item);

            if ($attribute instanceof Attribute && !$attribute->isDateTimeRelated()) {
                $values[$attrName] = $propertyValue;
                continue;
            }

            if ($attribute instanceof Attribute && $attribute->isDateTimeRelated()) {
                if ($propertyValue === null) {
                    $values[$attrName] = $propertyValue;
                    continue;
                }

                /** @var \DateTimeInterface $propertyValue */
                $values[$attrName] = $propertyValue->format($attribute->getFormat());
                continue;
            }

            if ($attribute instanceof NestedValueObjectAttribute) {
                $valueObjectReflection = new ReflectionClass($propertyValue);
                $valueObjectPropertyReflection = $valueObjectReflection->getProperty($attribute->getProperty());
                $valueObjectPropertyReflection->setAccessible(true);

                $values[$attrName] = $valueObjectPropertyReflection->getValue($propertyValue);
                continue;
            }

            if ($attribute instanceof NestedItemAttribute) {
                $nestedItemConfiguration = $itemMapping->getNestedItems()[$propertyName];
                $serializeMethod = $nestedItemConfiguration->getSerializeMethod();
                if ($serializeMethod !== null) {
                    $values[$attrName] = $propertyValue->$serializeMethod();
                    continue;
                }

                throw new DynamiteException('Getting nested items value via annotation not implemented yet');
            }

        }
        return $values;
    }

    public function hydrateObject(string $className, ItemMapping $itemMapping, array $data): object
    {
        $reflectionClass = new ReflectionClass($className);
        $instantiatedObject = $reflectionClass->newInstanceWithoutConstructor();

        foreach ($itemMapping->getPropertiesMapping() as $propertyName => $attribute) {
            $propertyReflection = $reflectionClass->getProperty($propertyName);
            $propertyReflection->setAccessible(true);
            $propValue = $data[$attribute->getName()];
            if ($attribute instanceof Attribute && !$attribute->isDateTimeRelated()) {
                $propertyReflection->setValue($instantiatedObject, $propValue);
                continue;
            }

            if ($attribute instanceof Attribute && $attribute->isDateTimeRelated()) {
                if ($propValue === null) {
                    $propertyReflection->setValue($instantiatedObject, $propValue);
                    continue;
                }

                $dateTime = \DateTime::createFromFormat($attribute->getFormat(), $propValue);
                if ($attribute->isImmutable()) {
                    $dateTime = \DateTimeImmutable::createFromMutable($dateTime);
                }

                $propertyReflection->setValue($instantiatedObject, $dateTime);
                continue;

            }

            if ($attribute instanceof NestedValueObjectAttribute) {
                $valueObjectFqcn = $attribute->getType();
                $valueObjectReflection = new ReflectionClass($valueObjectFqcn);
                $valueObjectInstance = $valueObjectReflection->newInstanceWithoutConstructor();
                $valueObjectProp = $valueObjectReflection->getProperty($attribute->getProperty());
                $valueObjectProp->setAccessible(true);
                $valueObjectProp->setValue($valueObjectInstance, $propValue);
                $propertyReflection->setValue($instantiatedObject, $valueObjectInstance);
                continue;
            }

            if ($attribute instanceof NestedItemAttribute) {
                $nestedItemFqcn = $attribute->getType();
                $nestedItemConfiguration = $itemMapping->getNestedItem($propertyName);
                $deserializeMethod = $nestedItemConfiguration->getDeserializeMethod();
                if ($deserializeMethod !== null) {
                    $propertyReflection->setValue($instantiatedObject, $nestedItemFqcn::$deserializeMethod($propValue));
                    continue;
                }

                throw new DynamiteException('Deserializing nested items value via annotation not implemented yet');
            }

        }

        return $instantiatedObject;
    }
}