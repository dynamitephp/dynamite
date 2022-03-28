<?php
declare(strict_types=1);

namespace Dynamite\PrimaryKey;


use Dynamite\Configuration\Attribute;
use Dynamite\Configuration\AttributeInterface;

interface ValueFilterInterface
{

    /**
     * @param mixed $value
     * @param Attribute $attribute
     * @return string
     */
    public function filter(mixed $value, AttributeInterface $attribute): string;
}