<?php
declare(strict_types=1);

namespace Dynamite\Configuration;


use Dynamite\Exception\ConfigurationException;

/**
 * A single scalar value which will be converted to object-with-single-property.
 * Use cases: Value objects with single values (eg: Colour, Currency)
 *
 * @Annotation
 *
 * @author pizzaminded <mikolajczajkowsky@gmail.com>
 * @license MIT
 */
class NestedValueObjectAttribute extends AbstractAttribute
{

    /**
     * Which property should hold value going to persistence layer?
     * @var string
     */
    protected string $property;


    public function __construct(array $props)
    {
        parent::__construct($props);
        $this->assertPropertiesPresence($props, ['property']);

        $this->property = $props['property'];
    }

    /**
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
}