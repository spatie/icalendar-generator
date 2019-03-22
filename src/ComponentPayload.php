<?php

namespace Spatie\Calendar;

use Closure;
use DateTimeInterface;
use Exception;
use http\Exception\RuntimeException;
use Spatie\Calendar\Components\Component;
use Spatie\Calendar\PropertyTypes\DateTimePropertyType;
use Spatie\Calendar\PropertyTypes\PropertyType;
use Spatie\Calendar\PropertyTypes\TextPropertyType;

class ComponentPayload
{
    /** @var string */
    protected $type;

    /** @var array */
    protected $properties = [];

    /** @var array */
    protected $subComponents = [];

    public static function new(string $type): ComponentPayload
    {
        return new self($type);
    }

    public function __construct(string $type)
    {
        $this->type = $type;
    }

    public function property(PropertyType $property): ComponentPayload
    {
        $this->properties[] = $property;

        return $this;
    }

    public function dateTimeProperty(
        string $name,
        ?DateTimeInterface $value,
        bool $withTime = false,
        bool $withTimeZone = false
    ): ComponentPayload {
        if ($value === null) {
            return $this;
        }

        return $this->property(new DateTimePropertyType($name, $value, $withTime, $withTimeZone));
    }

    public function textProperty(
        string $name,
        ?string $value
    ): ComponentPayload {
        if ($value === null) {
            return $this;
        }

        return $this->property(new TextPropertyType($name, $value));
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

    public function getProperty(string $name): PropertyType
    {
        $properties = array_values(array_filter(
            $this->properties,
            function (PropertyType $property) use ($name) {
                return $property->getName() === $name;
            }
        ));

        if(count($properties) === 0){
            throw new Exception('Property does not exist');
        }

        return $properties[0];
    }

    public function getSubComponents(): array
    {
        return $this->subComponents;
    }
}
