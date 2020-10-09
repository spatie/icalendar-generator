<?php

namespace Spatie\IcalendarGenerator;

use Closure;
use DateTimeInterface;
use Exception;
use Spatie\Enum\Enum;
use Spatie\IcalendarGenerator\Components\Component;
use Spatie\IcalendarGenerator\PropertyTypes\DateTimePropertyType;
use Spatie\IcalendarGenerator\PropertyTypes\PropertyType;
use Spatie\IcalendarGenerator\PropertyTypes\TextPropertyType;
use Spatie\IcalendarGenerator\PropertyTypes\UriPropertyType;

final class ComponentPayload
{
    /** @var string */
    private $type;

    /** @var array */
    private $properties = [];

    /** @var array */
    private $subComponents = [];

    public static function create(string $type): ComponentPayload
    {
        return new self($type);
    }

    public function __construct(string $type)
    {
        $this->type = $type;
    }

    public function property(PropertyType $property, array $parameters = null): ComponentPayload
    {
        $property->addParameters($parameters ?? []);

        $this->properties[] = $property;

        return $this;
    }

    /**
     * @param array|string $names
     * @param \DateTimeInterface|null $value
     * @param bool $withTime
     * @param bool $withTimeZone
     *
     * @return \Spatie\IcalendarGenerator\ComponentPayload
     */
    public function dateTimeProperty(
        $names,
        ?DateTimeInterface $value,
        bool $withTime = false,
        bool $withTimeZone = false
    ): ComponentPayload {
        if ($value === null) {
            return $this;
        }

        return $this->property(new DateTimePropertyType($names, $value, $withTime, $withTimeZone));
    }

    /**
     * @param array|string $names
     * @param string|\Spatie\Enum\Enum|null $value
     *
     * @param bool $disableEscaping
     *
     * @return \Spatie\IcalendarGenerator\ComponentPayload
     */
    public function textProperty(
        $names,
        ?string $value,
        bool $disableEscaping = false
    ): ComponentPayload {
        if ($value === null) {
            return $this;
        }

        if ($value instanceof Enum) {
            $value = (string) $value;
        }

        return $this->property(new TextPropertyType($names, $value, $disableEscaping));
    }

    /**
     * @param array|string $names
     * @param string|null $value
     *
     * @return \Spatie\IcalendarGenerator\ComponentPayload
     */
    public function uriProperty($names, ?string $value): ComponentPayload
    {
        if ($value === null) {
            return $this;
        }

        return filter_var($value, FILTER_VALIDATE_URL) ? $this->property(new UriPropertyType($names, $value)) : $this;
    }

    public function subComponent(Component ...$components): ComponentPayload
    {
        foreach ($components as $component) {
            $this->subComponents[] = $component;
        }

        return $this;
    }

    public function when(bool $condition, Closure $closure): ComponentPayload
    {
        if ($condition) {
            $closure($this);
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
        $filteredProperties = array_filter(
            $this->properties,
            function (PropertyType $property) use ($name) {
                return in_array($name, $property->getNames());
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
