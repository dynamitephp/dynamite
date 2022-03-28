<?php

declare(strict_types=1);

namespace Dynamite\Exception;


class ItemRepositoryException extends DynamiteException
{

    public static function objectNotSupported(string $unsupported, string $supported): self
    {
        return new self(sprintf('This ItemRepository instance supports only "%s" class, "%s" given', $supported, $unsupported));
    }

    public static function objectNotManaged(string $itemClass): self
    {
        return new self(sprintf('Given class "%s" is not managed by Dynamite.', $itemClass));
    }

    public static function noPropsInItem(string $itemClass): self
    {
        return new self(sprintf('Given class "%s" does not have any properties that Dynamite can process.', $itemClass));
    }
}