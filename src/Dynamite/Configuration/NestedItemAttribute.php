<?php
declare(strict_types=1);

namespace Dynamite\Configuration;

use Dynamite\Exception\ConfigurationException;

/**
 * Given attribute contains a class instead of scalar/array values.
 *
 * @Annotation
 *
 * @author pizzaminded <mikolajczajkowsky@gmail.com>
 * @license MIT
 */
class NestedItemAttribute extends AbstractAttribute
{

    /**
     * @param string $type
     * @return void
     */
    protected function assertType(string $type)
    {
        if (!class_exists($type)) {
            throw new ConfigurationException(sprintf('Class "%s" does not exists and cannot be used as a type in "%s" annotation.', $type, self::class));
        }
    }
}