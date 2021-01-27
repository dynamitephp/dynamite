<?php
declare(strict_types=1);

namespace Dynamite\Fixtures\Valid;


class CurrencyNestedValueObject
{
    private string $value;

    public function getValue(): string
    {
        return $this->value;
    }

    public function __construct(string $value)
    {
        $this->value = $value;
    }
}