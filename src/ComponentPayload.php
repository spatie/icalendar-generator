<?php

namespace Spatie\IcalendarGenerator;

use Exception;
use Spatie\IcalendarGenerator\Components\Component;
use Spatie\IcalendarGenerator\Properties\Parameter;
use Spatie\IcalendarGenerator\Properties\Property;

class ComponentPayload
{
    public static function create(string $type): ComponentPayload
    {
        return new self($type);
    }

    /**
     * @param Property[] $properties
     * @param Component[] $subComponents
     */
    public function __construct(
        protected string $type,
        protected array $properties = [],
        protected array $subComponents = [],
    ) {
    }

    /**
     * @param Parameter[]|null $parameters
     */
    public function property(Property $property, ?array $parameters = null): ComponentPayload
    {
        $property->addParameters($parameters ?? []);

        $this->properties[] = $property;

        return $this;
    }

    public function subComponent(Component ...$components): ComponentPayload
    {
        foreach ($components as $component) {
            $this->subComponents[] = $component;
        }

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    /** @return  array<Property> */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /** @return Property[]|Property */
    public function getProperty(string $name): Property|array
    {
        $filteredProperties = array_filter(
            $this->properties,
            function (Property $property) use ($name) {
                return in_array($name, $property->getNameAndAliases());
            }
        );

        $properties = array_values($filteredProperties);

        if (count($properties) === 0) {
            throw new Exception("Property `{$name}` does not exist in the payload");
        }

        if (count($properties) === 1) {
            return $properties[0];
        }

        return $properties;
    }

    /** @return Component[] */
    public function getSubComponents(): array
    {
        return $this->subComponents;
    }
}
