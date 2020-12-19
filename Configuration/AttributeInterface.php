<?php
declare(strict_types=1);

namespace Dynamite\Configuration;

/**
 * Interface AttributeInterface
 * @package Jadob\Dynamite\Configuration
 * @license MIT
 */
interface AttributeInterface
{
    public function getName(): string;
    public function getType(): string;

}