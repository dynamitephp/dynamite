<?php
declare(strict_types=1);

namespace Dynamite\Configuration;


/**
 * Declares that given class can be embedded in another classes.
 * Example use cases: Money Value Object
 *
 * @Annotation
 * @Target({"CLASS"})
 *
 * @author pizzaminded <mikolajczajkowsky@gmail.com>
 * @license MIT
 */
#[\Attribute(flags: \Attribute::TARGET_CLASS)]
class NestedItem
{
    /**
     * When set, payload from persistence layer will be passed as a first argument
     * Method must be public and static
     * @var string|null
     */
    private ?string $deserializeMethod;

    /**
     * When set, values from properties will be ignored and given method will be called
     * Function cannot be static, must be public and return array
     * @var string|null
     */
    private ?string $serializeMethod;

    public function __construct(array $props)
    {
        $this->deserializeMethod = $props['deserializeMethod'] ?? null;
        $this->serializeMethod = $props['serializeMethod'] ?? null;
    }

    /**
     * @return string|null
     */
    public function getSerializeMethod(): ?string
    {
        return $this->serializeMethod;
    }

    /**
     * @return string|null
     */
    public function getDeserializeMethod(): ?string
    {
        return $this->deserializeMethod;
    }


}