<?php

namespace Spatie\IcalendarGenerator\Builders;

use Spatie\IcalendarGenerator\PropertyTypes\PropertyType;

final class PropertyBuilder
{
    /** @var \Spatie\IcalendarGenerator\PropertyTypes\PropertyType */
    private $property;

    public function __construct(PropertyType $property)
    {
        $this->property = $property;
    }

    public function build(): array
    {
        $parameters = $this->resolveParameters();

        $value = $this->property->getValue();

        return array_map(function (string $name) use ($value, $parameters) {
            return "{$name}{$parameters}:{$value}";
        }, $this->property->getNames());
    }

    private function resolveParameters(): string
    {
        $parameters = '';

        foreach ($this->property->getParameters() as $parameter) {
            /** @var \Spatie\IcalendarGenerator\PropertyTypes\Parameter $parameter */
            $name = $parameter->getName();
            $value = $parameter->getValue();

            $parameters .= ";{$name}={$value}";
        }

        return $parameters;
    }
}
