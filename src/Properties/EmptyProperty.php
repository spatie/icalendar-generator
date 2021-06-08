<?php

namespace Spatie\IcalendarGenerator\Properties;

class EmptyProperty extends Property
{
    public static function create(string $name, array $parameters): EmptyProperty
    {
        return new self($name, $parameters);
    }

    public function __construct(string $name, array $parameters)
    {
        $this->name = $name;
        $this->parameters = $parameters;
    }

    public function getValue(): ?string
    {
        return null;
    }

    public function getOriginalValue()
    {
        return null;
    }
}
