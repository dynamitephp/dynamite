<?php
declare(strict_types=1);

namespace Dynamite\Configuration;

use Doctrine\Common\Annotations\Annotation\Required;
use Doctrine\Common\Annotations\Annotation\Target;
use Dynamite\Exception\ConfigurationException;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 * @Required()
 * @author pizzaminded <mikolajczajkowsky@gmail.com>
 * @license MIT
 */
class Attribute extends AbstractAttribute
{
    /**
     * @return void
     */
    protected function assertType(string $type)
    {
        $allowedValues = [
            'string',
            'string[]',
            'number',
            'number[]',
            'bool'
        ];

        if (!in_array($type, $allowedValues, true)) {
            throw ConfigurationException::invalidPropertyValue(self::class, 'type', $type);
        }
    }
}
