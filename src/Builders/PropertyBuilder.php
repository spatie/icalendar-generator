<?php

namespace Spatie\Calendar\Builders;

use Spatie\Calendar\PropertyTypes\Property;

class PropertyBuilder
{
    /** @var \Spatie\Calendar\PropertyTypes\Property */
    protected $property;

    public function __construct(Property $property)
    {
        $this->property = $property;
    }

    public function build(): string
    {
        $parameters = $this->resolveParameters();

        return "{$this->property->getName()}{$parameters}:{$this->property->getValue()}";
    }

    protected function resolveParameters(): string
    {
        $parameters = '';

        foreach ($this->property->getParameters() as $parameter) {
            /** @var \Spatie\Calendar\PropertyTypes\Property $parameter */
            $name = $parameter->getName();
            $value = $parameter->getValue();

            $parameters .= ";{$name}={$value}";
        }

        return $parameters;
    }
}
