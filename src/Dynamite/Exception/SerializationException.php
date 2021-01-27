<?php
declare(strict_types=1);

namespace Dynamite\Exception;


class SerializationException extends DynamiteException
{

    public static function propIsNotArray(string $propName, string $fqcn, $value)
    {
        return new self(sprintf('Property "%s" in "%s" expects an array, "%s" given', $propName, $fqcn, gettype($value)));

    }
}