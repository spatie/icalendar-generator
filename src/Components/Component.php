<?php

namespace Spatie\IcalendarGenerator\Components;

use Spatie\IcalendarGenerator\ComponentPayload;
use Spatie\IcalendarGenerator\Builders\ComponentBuilder;
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
        $providedProperties = [];

        /** @var \Spatie\IcalendarGenerator\PropertyTypes\PropertyType $property */
        foreach ($componentPayload->getProperties() as $property) {
            $providedProperties = array_merge(
                $providedProperties,
                $property->getNames()
            );
        }

        $requiredProperties = $this->getRequiredProperties();

        $intersection = array_intersect($requiredProperties, $providedProperties);

        if (count($intersection) !== count($requiredProperties)) {
            $missingProperties = array_diff($requiredProperties, $intersection);

            throw InvalidComponent::requiredPropertyMissing($missingProperties, $this);
        }
    }
}
