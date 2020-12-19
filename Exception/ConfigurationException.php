<?php
declare(strict_types=1);

namespace Dynamite\Exception;


/**
 * @author pizzaminded <mikolajczajkowsky@gmail.com>
 * @license MIT
 * @final
 */
class ConfigurationException extends DynamiteException
{
    public static function missingAnnotationProperty(string $annotationFqcn, string $propName)
    {
        return new self(sprintf('Missing "%s" property in "%s" annotation.', $propName, $annotationFqcn));
    }

    public static function invalidPropertyValue(string $annotationFqcn, string $propName, string $invalidValue)
    {
        return new self(sprintf('"%s" is not a valid value for property "%s" in "%s" annotation.', $invalidValue, $propName, $annotationFqcn));
    }
}