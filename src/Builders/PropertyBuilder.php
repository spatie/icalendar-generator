<?php

namespace Spatie\IcalendarGenerator\Builders;

use Spatie\IcalendarGenerator\Properties\Property;

class PropertyBuilder
{
    public function __construct(protected Property $property)
    {
    }

    /**
     * @return array<string>
     */
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

    protected function resolveParameters(): string
    {
        $parameters = '';

        foreach ($this->property->getParameters() as $parameter) {
            $name = $parameter->getName();
            $value = $parameter->getValue();

            $parameters .= ";{$name}={$value}";
        }

        return $parameters;
    }
}
