<?php

declare(strict_types=1);

namespace Dynamite;

use Dynamite\Configuration\Attribute;
use Dynamite\Configuration\NestedItemAttribute;
use Dynamite\Configuration\NestedValueObjectAttribute;
use Dynamite\Exception\DynamiteException;
use Dynamite\Exception\SerializationException;
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
        $output = [];
        $serializedContent = $this->serializeToPropNames($item, $itemMapping);

        foreach ($serializedContent as $propName => $value) {
            $attributeName = $itemMapping->getProperty($propName)->getName();
            $output[$attributeName] = $value;
        }

        return $output;
    }

    public function serializeToPropNames(object $item, ItemMapping $itemMapping): array
    {
        $values = [];
        $reflectionClass = new ReflectionClass($item);
        foreach ($itemMapping->getPropertiesMapping() as $propertyName => $attribute) {
            $propertyReflection = $reflectionClass->getProperty($propertyName);
            $propertyReflection->setAccessible(true);

            if (!$propertyReflection->isInitialized($item)) {
                throw SerializationException::propIsNotInitialized($propertyName, get_class($item));
            }

            $propertyValue = $propertyReflection->getValue($item);

            if ($attribute instanceof Attribute && !$attribute->isDateTimeRelated()) {
                $shorteners = $itemMapping->getShortenersForProp($propertyName);

                foreach ($shorteners as $shortener) {
                    if ($propertyValue === $shortener->getFrom()) {
                        $values[$propertyName] = $shortener->getTo();
                        continue 2;
                    }
                }

                $values[$propertyName] = $propertyValue;
                continue;
            }

            if ($attribute instanceof Attribute && $attribute->isDateTimeRelated()) {
                if ($propertyValue === null) {
                    $values[$propertyName] = $propertyValue;
                    continue;
                }

                /** @var \DateTimeInterface $propertyValue */
                $values[$propertyName] = $propertyValue->format($attribute->getFormat());
                continue;
            }

            if ($attribute instanceof NestedValueObjectAttribute) {
                if ($attribute->isCollection()) {
                    if (!is_array($propertyValue)) {
                        throw SerializationException::propIsNotArray($propertyName, get_class($item), $propertyValue);
                    }

                    $output = [];
                    foreach ($propertyValue as $val) {
                        $output[] = $this->nestedValueObjectToScalar($attribute, $val);
                    }

                    $values[$propertyName] = $output;
                    continue;
                }
                $values[$propertyName] = $this->nestedValueObjectToScalar($attribute, $propertyValue);
                continue;
            }

            if ($attribute instanceof NestedItemAttribute) {
                $nestedItemConfiguration = $itemMapping->getNestedItems()[$propertyName];
                $serializeMethod = $nestedItemConfiguration->getSerializeMethod();
                if ($serializeMethod !== null) {
                    $values[$propertyName] = $propertyValue->$serializeMethod();
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

            $propValue = null;
            if (array_key_exists($attribute->getName(), $data)) {
                $propValue = $data[$attribute->getName()];
            }

            if (
                $propertyReflection->getType() !== null                 // property is typed
                && !$propertyReflection->getType()->allowsNull()        // is not nullable
                && (
                    !array_key_exists($attribute->getName(), $data)     // nothing received from DB
                    || (
                        array_key_exists($attribute->getName(), $data)  // or got something
                        && $data[$attribute->getName()] === null        // ... but turns out is NULL
                    )
                )

            ) {
                throw SerializationException::valIsMissingButPropIsNotNullable($propertyName, $className);
            }

            if ($attribute instanceof Attribute && !$attribute->isDateTimeRelated()) {
                $shorteners = $itemMapping->getShortenersForProp($propertyName);

                foreach ($shorteners as $shortener) {
                    if ($propValue === $shortener->getTo()) {
                        $propertyReflection->setValue($instantiatedObject, $shortener->getFrom());
                        continue 2;
                    }
                }

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
                if ($attribute->isCollection()) {
                    if (!is_array($propValue)) {
                        throw SerializationException::propIsNotArray($propertyName, $className, $propValue);
                    }

                    $output = [];
                    foreach ($propValue as $prop) {
                        $output[] = $this->scalarToNestedValueObject($attribute, $prop);
                    }

                    $propertyReflection->setValue($instantiatedObject, $output);
                    continue;
                }

                $valueObjectInstance = $this->scalarToNestedValueObject($attribute, $propValue);
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

    /**
     * @return string|int|bool|null
     *
     * @throws \ReflectionException
     */
    protected function nestedValueObjectToScalar(NestedValueObjectAttribute $nestedVO, object $propValue)
    {
        $valueObjectReflection = new ReflectionClass($propValue);
        $valueObjectPropertyReflection = $valueObjectReflection->getProperty($nestedVO->getProperty());
        $valueObjectPropertyReflection->setAccessible(true);

        return $valueObjectPropertyReflection->getValue($propValue);
    }

    /**
     * @param string|int|bool|null $propValue
     *
     * @throws \ReflectionException
     */
    protected function scalarToNestedValueObject(NestedValueObjectAttribute $nestedVO, $propValue): object
    {
        $valueObjectFqcn = $nestedVO->getType();
        $valueObjectReflection = new ReflectionClass($valueObjectFqcn);
        $valueObjectInstance = $valueObjectReflection->newInstanceWithoutConstructor();
        $valueObjectProp = $valueObjectReflection->getProperty($nestedVO->getProperty());
        $valueObjectProp->setAccessible(true);
        $valueObjectProp->setValue($valueObjectInstance, $propValue);

        return $valueObjectInstance;
    }
}
