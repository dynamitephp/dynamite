<?php
declare(strict_types=1);

namespace Dynamite\Configuration;

/**
 * Defines the format of Partition Key which will be stored in DB.
 * @Annotation
 *
 * @author pizzaminded <mikolajczajkowsky@gmail.com>
 * @license MIT
 */
#[\Attribute(flags: \Attribute::TARGET_CLASS)]
class PartitionKeyFormat
{
    /**
     * @Required
     * @var string
     */
    public string $value;

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
}
