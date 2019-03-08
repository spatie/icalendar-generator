<?php

namespace Spatie\Calendar;

use DateTimeInterface;
use Spatie\Calendar\Components\Component;
use Spatie\Calendar\PropertyTypes\DateTimeProperty;
use Spatie\Calendar\PropertyTypes\Property;
use Spatie\Calendar\PropertyTypes\TextProperty;

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

    public function property(Property ...$properties): ComponentPayload
    {
        foreach ($properties as $property) {
            $this->properties[] = $property;
        }

        return $this;
    }

    public function dateTimeProperty(string $name, ?DateTimeInterface $value): ComponentPayload
    {
        if ($value === null) {
            return $this;
        }

        return $this->property(new DateTimeProperty($name, $value));
    }

    public function textProperty(string $name, ?string $value): ComponentPayload
    {
        if ($value === null) {
            return $this;
        }

        return $this->property(new TextProperty($name, $value));
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

    public function getSubComponents(): array
    {
        return $this->subComponents;
    }
}
