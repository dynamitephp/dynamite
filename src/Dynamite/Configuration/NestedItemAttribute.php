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
#[\Attribute(flags: \Attribute::TARGET_PROPERTY)]
class NestedItemAttribute implements AttributeInterface
{

    /**
     * @throws ConfigurationException
     */
    public function __construct(
        protected string $type,
        protected string $name,
        private bool $collection = false
    ) {
        $this->assertType($this->type);
    }

    /**
     * @param string $type
     * @return void
     * @throws ConfigurationException
     */
    protected function assertType(string $type): void
    {
        if (!class_exists($type)) {
            throw new ConfigurationException(sprintf('Class "%s" does not exists and cannot be used as a type in "%s" attribute.', $type, self::class));
        }
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getName(): string
    {
        return $this->name;
    }
    
    public function isCollection(): bool
    {
        return $this->collection;
    }
}