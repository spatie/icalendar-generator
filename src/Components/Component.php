<?php

namespace Spatie\IcalendarGenerator\Components;

use Spatie\IcalendarGenerator\ComponentPayload;
use Spatie\IcalendarGenerator\Builders\ComponentBuilder;
use Spatie\IcalendarGenerator\PropertyTypes\PropertyType;
use Spatie\IcalendarGenerator\Exceptions\InvalidComponent;

abstract class Component
{
    abstract public function getComponentType(): string;

    abstract public function getRequiredProperties(): array;

    abstract public function getPayload(): ComponentPayload;

    public function toString(): string
    {
        $payload = $this->getPayload();

        $this->ensureRequiredPropertiesAreSet($payload);

        $builder = new ComponentBuilder($payload);

        return $builder->build();
    }

    protected function ensureRequiredPropertiesAreSet(ComponentPayload $componentPayload)
    {
        $requiredProperties = $this->getRequiredProperties();

        $providedProperties = array_map(function (PropertyType $property) {
            return $property->getName();
        }, $componentPayload->getProperties());

        $intersection = array_intersect($requiredProperties, $providedProperties);

        if (count($intersection) !== count($requiredProperties)) {
            $notProvidedProperties = array_diff($requiredProperties, $intersection);

            throw InvalidComponent::requiredPropertyMissing($notProvidedProperties, $this);
        }
    }
}
