<?php

namespace Spatie\IcalendarGenerator\Properties;

class EmptyProperty extends Property
{
    /**
     * @param array<Parameter> $parameters
     */
    public static function create(string $name, array $parameters): EmptyProperty
    {
        return new self($name, $parameters);
    }

    /**
     * @param array<Parameter> $parameters
     */
    public function __construct(string $name, array $parameters)
    {
        $this->name = $name;
        $this->parameters = $parameters;
    }

    public function getValue(): ?string
    {
        return null;
    }

    public function getOriginalValue(): mixed
    {
        return null;
    }
}
