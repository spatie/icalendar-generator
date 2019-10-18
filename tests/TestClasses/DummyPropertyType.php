<?php

namespace Spatie\IcalendarGenerator\Tests\TestClasses;

use Spatie\IcalendarGenerator\PropertyTypes\PropertyType;

class DummyPropertyType extends PropertyType
{
    /** @var string */
    protected $value;

    /**
     * DummyPropertyType constructor.
     *
     * @param string|array $names
     * @param string $value
     */
    public function __construct($names, string $value)
    {
        parent::__construct($names);

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
