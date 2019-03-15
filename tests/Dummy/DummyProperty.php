<?php

namespace Spatie\Calendar\Tests\Dummy;

use Spatie\Calendar\PropertyTypes\Property;

class DummyProperty extends Property
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
