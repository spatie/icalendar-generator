<?php

namespace Spatie\IcalendarGenerator\PropertyTypes;

use Exception;

abstract class PropertyType
{
    /** @var array */
    protected $names;

    /** @var array */
    protected $parameters = [];

    abstract public function getValue(): string;

    abstract public function getOriginalValue();

    /**
     * PropertyType constructor.
     *
     * @param $names array|string
     */
    public function __construct($names)
    {
        $this->names = is_string($names)
            ? [$names]
            : $names;
    }

    public function getNames(): array
    {
        return $this->names;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function getParameter(string $name): Parameter
    {
        $parameters = array_values(array_filter(
            $this->parameters,
            function (Parameter $property) use ($name) {
                return $property->getName() === $name;
            }
        ));

        if (count($parameters) === 0) {
            throw new Exception("Parameter {$name} does not exist in the property.");
        }

        return $parameters[0];
    }

    public function addParameters(array $parameters): PropertyType
    {
        foreach ($parameters as $parameter) {
            $this->addParameter($parameter);
        }

        return $this;
    }

    public function addParameter(Parameter $parameter): PropertyType
    {
        $this->parameters[] = $parameter;

        return $this;
    }
}
