<?php
declare(strict_types=1);

namespace Dynamite\Configuration;

use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use Doctrine\Common\Annotations\Annotation\Required;
use Doctrine\Common\Annotations\Annotation\Target;
use Dynamite\Exception\ConfigurationException;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 * @NamedArgumentConstructor()
 * @Required()
 * @author pizzaminded <mikolajczajkowsky@gmail.com>
 * @license MIT
 */
#[\Attribute(flags: \Attribute::TARGET_PROPERTY)]
class Attribute extends AbstractAttribute
{
    public const TYPE_STRING = 'string';
    public const TYPE_TIMESTAMP = 'timestamp';
    public const TYPE_DATETIME = 'datetime';

    /**
     * DateTime#format() syntax applies here.
     * @var string
     */
    protected string $format = 'U';

    /**
     * When true, an DateTimeImmutable will be created instead of DateTime.
     * @var bool
     */
    protected bool $immutable = false;

    public function __construct(
        string $name,
        string $type,
        bool $immutable = false,
        string $format = 'U'
    )
    {
        $this->immutable = $immutable;
        $this->format = $format;

        parent::__construct([
            'type' => $type,
            'name' => $name
        ]);
    }

    /**
     * @return void
     */
    protected function assertType(string $type)
    {
        $allowedValues = [
            self::TYPE_STRING,
            'string[]',
            'number',
            'number[]',
            'bool',
            self::TYPE_TIMESTAMP,
            self::TYPE_DATETIME
        ];

        if (!in_array($type, $allowedValues, true)) {
            throw ConfigurationException::invalidPropertyValue(self::class, 'type', $type);
        }

        #
        # @TODO: datetime format validation
        #
    }

    /**
     * @return string
     */
    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * @return bool
     */
    public function isImmutable(): bool
    {
        return $this->immutable;
    }

    public function isDateTimeRelated(): bool
    {
        if ($this->type === self::TYPE_DATETIME) {
            return true;
        }

        if ($this->type === self::TYPE_TIMESTAMP) {
            return true;
        }

        return false;
    }
}
