<?php

namespace Spatie\Calendar\Builders;

use Spatie\Calendar\PropertyTypes\PropertyType;

final class PropertyBuilder
{
    /** @var \Spatie\Calendar\PropertyTypes\PropertyType */
    private $property;

    public function __construct(PropertyType $property)
    {
        $this->property = $property;
    }

    public function build($name = null): string
    {
        $name = $name ?? $this->property->getName();

        $parameters = $this->resolveParameters();

        $value = $this->property->getValue();

        return "{$name}{$parameters}:{$value}";
    }

    private function resolveParameters(): string
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
