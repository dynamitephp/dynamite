<?php

namespace Dynamite\Fixtures;

use Dynamite\Configuration\NestedItem;

#[NestedItem(deserializeMethod: 'fromArray', serializeMethod: 'toArray')]
class IdentityCard
{
    public function __construct(
        public string $type,
        public string $number,
    )
    {
    }

    public function toArray()
    {
        return [
            'type' => $this->type,
            'number' => $this->number
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self($data['type'], $data['number']);
    }
}