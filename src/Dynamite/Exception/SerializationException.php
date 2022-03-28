<?php

declare(strict_types=1);

namespace Dynamite\Exception;

class SerializationException extends DynamiteException
{
    public static function propIsNotArray(string $propName, string $fqcn, $value)
    {
        return new self(sprintf('Property "%s" in "%s" expects an array, "%s" given', $propName, $fqcn, gettype($value)));
    }

    /**
     * Called when there is a property pointed to be an Attribute, but is havent been touched.
     */
    public static function propIsNotInitialized(string $propName, string $fqcn): SerializationException
    {
        return new self(sprintf('Cannot serialize an item "%s" as property "%s" is not initialized.', $fqcn, $propName));
    }
}
