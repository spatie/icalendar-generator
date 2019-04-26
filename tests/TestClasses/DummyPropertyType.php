<?php

namespace Spatie\IcalendarGenerator\Tests\TestClasses;

use Spatie\IcalendarGenerator\PropertyTypes\PropertyType;

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
