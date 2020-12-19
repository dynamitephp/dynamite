<?php

declare(strict_types=1);

namespace Dynamite\Exception;

/**
 * @author pizzaminded <mikolajczajkowsky@gmail.com>
 * @license MIT
 */
class MissingItemMappingException extends DynamiteException
{
    /**
     * @param string $itemFqcn
     * @return MissingItemMappingException
     */
    public static function forItem(string $itemFqcn)
    {
        return new self(
            sprintf(
                'There is no item configuration for "%s" class',
                $itemFqcn
            )
        );
    }
}
