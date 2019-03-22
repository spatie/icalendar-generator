<?php

namespace Spatie\Calendar\Builders;

use Spatie\Calendar\PropertyTypes\PropertyType;

class PropertyBuilder
{
    /** @var \Spatie\Calendar\PropertyTypes\PropertyType */
    protected $property;

    public function __construct(PropertyType $property)
    {
        $this->property = $property;
    }

    public function build(): string
    {
        $parameters = $this->resolveParameters();

        $value = $this->property->getValue();

        return "{$this->property->getName()}{$parameters}:{$value}";
    }

    protected function resolveParameters(): string
    {
        $parameters = '';

        foreach ($this->property->getParameters() as $parameter) {
            /** @var \Spatie\Calendar\PropertyTypes\PropertyType $parameter */
            $name = $parameter->getName();
            $value = $parameter->getValue();

            $parameters .= ";{$name}={$value}";
        }

        return $parameters;
    }
}
