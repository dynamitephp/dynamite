<?php

declare(strict_types=1);

namespace Dynamite\Exception;


class ItemRepositoryException extends DynamiteException
{

    public static function objectNotSupported(string $unsupported, string $supported)
    {
        return new self(sprintf('This instance ItemRepository supports only "%s" class, "%s" given', $supported, $unsupported));
    }
}