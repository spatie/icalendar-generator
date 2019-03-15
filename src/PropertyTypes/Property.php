<?php


namespace Spatie\Calendar\PropertyTypes;


abstract class Property
{
    /** @var string */
    protected $name;

    /** @var array */
    protected $parameters = [];

    public abstract function getValue(): string;

    public abstract function getOriginalValue();

    public function getName(): string
    {
        return $this->name;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }
}
