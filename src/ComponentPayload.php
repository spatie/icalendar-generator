<?php

namespace Spatie\IcalendarGenerator;

use Closure;
use Exception;
use Spatie\IcalendarGenerator\Components\Component;
use Spatie\IcalendarGenerator\Properties\Property;

class ComponentPayload
{
    private string $type;

    private array $properties = [];

    private array $subComponents = [];

    public static function create(string $type): ComponentPayload
    {
        return new self($type);
    }

    public function __construct(string $type)
    {
        $this->type = $type;
    }

    public function property(Property $property, array $parameters = null): ComponentPayload
    {
        $property->addParameters($parameters ?? []);

        $this->properties[] = $property;

        return $this;
    }

    public function optional($when, Closure $closure): self
    {
        if ($when === null || $when === false) {
            return $this;
        }

        $this->properties[] = $closure();

        return $this;
    }

    public function multiple(array $items, Closure $closure): self
    {
        foreach ($items as $item) {
            $this->property($closure($item));
        }

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

    public function getProperties(): array
    {
        return $this->properties;
    }

    public function getProperty(string $name): Property
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

        return $properties[0];
    }

    public function getSubComponents(): array
    {
        return $this->subComponents;
    }
}
