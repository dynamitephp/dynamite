<?php
declare(strict_types=1);

namespace Dynamite\Configuration;


/**
 * Tells serializer to convert a longer value into a short one and store them in DB, and to convert a short one to longer
 * while hydrating an object from DB.
 *
 * @Annotation
 * @Target({"PROPERTY"})
 *
 * @author pizzaminded <mikolajczajkowsky@gmail.com>
 * @license MIT
 */
#[\Attribute(flags: \Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class Shorten
{

    public function __construct(
        protected string|int $from,
        protected string|int $to,
    ) {}

    /**
     * @return int|string
     */
    public function getFrom(): int|string
    {
        return $this->from;
    }

    /**
     * @return int|string
     */
    public function getTo(): int|string
    {
        return $this->to;
    }

}