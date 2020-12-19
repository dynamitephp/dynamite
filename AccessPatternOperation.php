<?php
declare(strict_types=1);

namespace Dynamite;


class AccessPatternOperation
{

    private const QUERY = 0x00;
    private int $value;

    public static function query()
    {
        return new self(self::QUERY);
    }

    private function __construct(int $value)
    {
        $this->value = $value;
    }

    public function isQuery(): bool
    {
        return $this->value === self::QUERY;
    }
}