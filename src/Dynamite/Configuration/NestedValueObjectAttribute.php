<?php
declare(strict_types=1);

namespace Dynamite\Configuration;


use Dynamite\Exception\ConfigurationException;

/**
 * A object-with-single-property (or array of them) which will be converted to scalar value (or array of them).
 * Use cases: Value objects with single values (eg: Colour, Currency)
 *
 * @author pizzaminded <mikolajczajkowsky@gmail.com>
 * @license MIT
 */
#[\Attribute(flags: \Attribute::TARGET_PROPERTY)]
class NestedValueObjectAttribute extends AbstractAttribute
{
    public function __construct(
        string $type,
        string $name,
        /**
         * Which property should hold value going to persistence layer?
         * @var string
         */
        protected string $property,
        protected bool $collection = false
    ) {

        parent::__construct([
            'type' => $type,
            'property' => $property,
            'name' => $name,

        ]);
    }

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

    /**
     * @return string
     */
    public function getProperty(): string
    {
        return $this->property;
    }

    /**
     * @return bool
     */
    public function isCollection(): bool
    {
        return $this->collection;
    }

}