<?php
declare(strict_types=1);

namespace Dynamite\Configuration;

use Doctrine\Common\Annotations\Annotation\Target;

/**
 * Indicates that given property is an partition key.
 *
 * @Annotation
 * @Target({"PROPERTY"})
 *
 * @author pizzaminded <mikolajczajkowsky@gmail.com>
 * @license MIT
 */
#[\Attribute(flags: \Attribute::TARGET_PROPERTY)]
class PartitionKey
{
}
