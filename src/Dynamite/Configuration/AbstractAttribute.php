<?php
declare(strict_types=1);

namespace Dynamite\Configuration;


use Dynamite\Exception\ConfigurationException;

/**
 * @author pizzaminded <mikolajczajkowsky@gmail.com>
 * @license MIT
 * @internal
 * @deprecated
 */
abstract class AbstractAttribute implements AttributeInterface
{
    /**
     * @var string
     */
    protected string $type;

    /**
     * @var string
     */
    protected string $name;


    public function __construct(array $props)
    {
        $this->assertPropertiesPresence($props, ['type', 'name']);
        $this->assertType($props['type']);

        $this->type = $props['type'];
        $this->name = $props['name'];
    }

    /**
     * @param array<string, string> $props
     * @param string[] $requiredProps
     *
     * @return void
     */
    protected function assertPropertiesPresence(array $props, array $requiredProps): void
    {
        foreach ($requiredProps as $requiredProp) {
            if (!isset($props[$requiredProp])) {
                throw ConfigurationException::missingAnnotationProperty(static::class, $requiredProp);
            }
        }
    }

    abstract protected function assertType(string $type);

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }
}