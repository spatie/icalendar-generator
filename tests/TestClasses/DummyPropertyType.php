<?php

namespace Spatie\IcalendarGenerator\Tests\TestClasses;

use Spatie\IcalendarGenerator\PropertyTypes\PropertyType;

class DummyPropertyType extends PropertyType
{
    protected string $value;

    public function __construct($name, string $value)
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
