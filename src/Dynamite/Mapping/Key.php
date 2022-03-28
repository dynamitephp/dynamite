<?php
declare(strict_types=1);

namespace Dynamite\Mapping;


/**
 * @deprecated - unless i will remind myself why i created this class
 */
class Key
{

    private string $keyFormat;
    private string $property;
    public function __construct(string $keyFormat, string $property)
    {
        $this->keyFormat = $keyFormat;
        $this->property = $property;
    }

    /**
     * @return string
     */
    public function getKeyFormat(): string
    {
        return $this->keyFormat;
    }

    /**
     * @return string
     */
    public function getProperty(): string
    {
        return $this->property;
    }

}