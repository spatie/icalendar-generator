<?php


namespace Spatie\Calendar\PropertyTypes;

use Exception;

abstract class PropertyType
{
    /** @var string */
    protected $name;

    /** @var array */
    protected $parameters = [];

    abstract public function getValue(): string;

    abstract public function getOriginalValue();

    public function getName(): string
    {
        return $this->name;
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
        foreach($parameters as $parameter) {
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
