<?php

declare(strict_types=1);

namespace Dynamite\Exception;

class FormatException extends DynamiteException
{
    /**
     * @param string $missingFilter
     * @return $this
     */
    public static function filterNotFound(string $missingFilter): FormatException
    {
        return new self(sprintf('Filter "%s" not found.', $missingFilter));
    }
}
