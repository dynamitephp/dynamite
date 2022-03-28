<?php

declare(strict_types=1);

namespace Dynamite\PrimaryKey\Filter;

use Dynamite\Configuration\Attribute;
use Dynamite\Configuration\AttributeInterface;
use Dynamite\PrimaryKey\ValueFilterInterface;

class Md5Filter implements ValueFilterInterface
{
    public function filter(mixed $value, AttributeInterface $attribute): string
    {
        return md5($value);
    }
}
