<?php

namespace Spatie\IcalendarGenerator\Tests\TestClasses;

use Spatie\IcalendarGenerator\Properties\Property;

class DummyProperty extends Property
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
