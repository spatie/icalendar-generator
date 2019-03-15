<?php


namespace Spatie\Calendar\PropertyTypes;


use Exception;

abstract class Property
{
    /** @var string */
    protected $name;

    /** @var array */
    protected $parameters = [];

    public abstract function getValue(): string;

    public abstract function getOriginalValue();

    public function getName(): string
    {
        return $this->name;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function getParameter(string $name): Property
    {
        $parameters = array_values(array_filter(
            $this->parameters,
            function (Property $property) use ($name) {
                return $property->getName() === $name;
            }
        ));

        if(count($parameters) === 0){
            throw new Exception('Parameter does not exist');
        }

        return $parameters[0];
    }

    public function addParameter(Property $property): Property
    {
        $this->parameters[] = $property;

        return $this;
    }
}
