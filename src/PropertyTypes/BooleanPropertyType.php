<?php

namespace Spatie\Calendar\PropertyTypes;

final class BooleanPropertyType extends PropertyType
{
    /** @var bool */
    private $boolean;

    public function __construct(string $name, bool $boolean)
    {
        $this->name = $name;

        $this->boolean = $boolean;
    }

    public function getValue(): string
    {
        return $this->boolean ? 'TRUE' : 'FALSE';
    }

    public function getOriginalValue() : bool
    {
        return $this->boolean;
    }
}
