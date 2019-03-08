<?php

namespace Spatie\Calendar\PropertyTypes;

class BooleanProperty extends Property
{
    /** @var bool */
    protected $boolean;

    public function __construct(string $name, bool $boolean)
    {
        $this->name = $name;
        $this->boolean = $boolean;
    }

    public function getValue(): string
    {
        return $this->boolean ? 'TRUE' : 'FALSE';
    }
}
