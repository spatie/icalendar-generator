<?php

namespace Spatie\Calendar\Tests\TestClasses;

use Spatie\Calendar\PropertyTypes\PropertyType;

class DummyPropertyType extends PropertyType
{
    /** @var string */
    protected $value;

    public function __construct(string $name, string $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getOriginalValue() : string
    {
        return $this->value;
    }
}
