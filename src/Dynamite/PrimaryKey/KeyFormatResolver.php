<?php

declare(strict_types=1);

namespace Dynamite\PrimaryKey;

use Dynamite\Exception\FormatException;
use Dynamite\Mapping\ItemMapping;

class KeyFormatResolver
{
    protected const PLACEHOLDER_REGEXP = '/{(.*?)\}/';

    /**
     * @var array<string, ValueFilterInterface>
     */
    protected array $filters = [];

    public function resolve(string $format, ItemMapping $mapping, array $values): string
    {
        preg_match_all(self::PLACEHOLDER_REGEXP, $format, $matches);

        $placeholders = $matches[1] ?? [];

        foreach ($placeholders as $key => $placeholder) {
            $filters = [];
            $propName = $placeholder;
            if (str_contains($placeholder, ':')) {
                $splitPlaceholder = explode(':', $placeholder);
                $propName = array_pop($splitPlaceholder);
                $filters = $splitPlaceholder;
            }

            $attribute = $mapping->getProperty($propName);
            $valueToInterpolate = $values[$propName];

            foreach ($filters as $filter) {
                if(!isset($this->filters[$filter])) {
                    throw FormatException::filterNotFound($filter);
                }

                $valueToInterpolate = $this->filters[$filter]
                    ->filter(
                        $valueToInterpolate,
                        $attribute
                    );
            }

            $format = str_replace($matches[0][$key], $valueToInterpolate, $format);


        }

        return $format;
    }

    public function addFilter(string $name, ValueFilterInterface $filter): void
    {
        $this->filters[$name] = $filter;
    }
}
