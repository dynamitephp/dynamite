<?php
declare(strict_types=1);

namespace Dynamite\Configuration;

use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;

/**
 * Defines the format of Partition Key which will be stored in DB.
 * @Annotation
 * @NamedArgumentConstructor()
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

    public function __construct(
        string $value
    )
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
}
