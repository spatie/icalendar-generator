<?php

namespace Spatie\IcalendarGenerator\Builders;

use Spatie\IcalendarGenerator\Properties\Property;

class PropertyBuilder
{
    /** @var \Spatie\IcalendarGenerator\Properties\Property */
    private Property $property;

    public function __construct(Property $property)
    {
        $this->property = $property;
    }

    public function build(): array
    {
        $parameters = $this->resolveParameters();

        $value = $this->property->getValue();

        return array_map(
            fn (string $name) => $value !== null
                ? "{$name}{$parameters}:{$value}"
                : "{$name}{$parameters}",
            $this->property->getNameAndAliases()
        );
    }

    private function resolveParameters(): string
    {
        $parameters = '';

        foreach ($this->property->getParameters() as $parameter) {
            /** @var \Spatie\IcalendarGenerator\Properties\Parameter $parameter */
            $name = $parameter->getName();
            $value = $parameter->getValue();

            $parameters .= ";{$name}={$value}";
        }

        return $parameters;
    }
}
