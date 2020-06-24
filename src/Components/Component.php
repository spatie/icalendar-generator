<?php

namespace Spatie\IcalendarGenerator\Components;

use Spatie\IcalendarGenerator\Builders\ComponentBuilder;
use Spatie\IcalendarGenerator\ComponentPayload;
use Spatie\IcalendarGenerator\Exceptions\InvalidComponent;
use Spatie\IcalendarGenerator\Properties\Property;

abstract class Component
{
    /** @var \Spatie\IcalendarGenerator\Properties\Property[] */
    private array $appendedProperties = [];

    /** @var \Spatie\IcalendarGenerator\Components\Component[] */
    private array $appendedSubComponents = [];

    abstract public function getComponentType(): string;

    abstract public function getRequiredProperties(): array;

    abstract protected function payload(): ComponentPayload;

    public function resolvePayload(): ComponentPayload
    {
        $payload = $this->payload();

        foreach ($this->appendedProperties as $appendedProperty) {
            $payload->property($appendedProperty);
        }

        $payload->subComponent(...$this->appendedSubComponents);

        return $payload;
    }

    public function toString(): string
    {
        $payload = $this->resolvePayload();

        $this->ensureRequiredPropertiesAreSet($payload);

        $builder = new ComponentBuilder($payload);

        return $builder->build();
    }

    public function appendProperty(Property $property): Component
    {
        $this->appendedProperties[] = $property;

        return $this;
    }

    public function appendSubComponent(Component $component): Component
    {
        $this->appendedSubComponents[] = $component;

        return $this;
    }

    protected function ensureRequiredPropertiesAreSet(ComponentPayload $componentPayload)
    {
        $providedProperties = [];

        /** @var \Spatie\IcalendarGenerator\Properties\Property $property */
        foreach ($componentPayload->getProperties() as $property) {
            $providedProperties = array_merge(
                $providedProperties,
                $property->getNameAndAliases()
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
