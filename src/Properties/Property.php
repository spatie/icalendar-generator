<?php

namespace Spatie\IcalendarGenerator\Properties;

use Exception;

abstract class Property
{
    protected string $name;

    protected array $parameters = [];

    protected array $aliases = [];

    abstract public function getValue(): ?string;

    abstract public function getOriginalValue();

    public function getNameAndAliases(): array
    {
        return array_merge(
            [$this->name],
            $this->aliases
        );
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAliases(): array
    {
        return $this->aliases;
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

    public function addParameters(array $parameters): self
    {
        foreach ($parameters as $parameter) {
            $this->addParameter($parameter);
        }

        return $this;
    }

    public function addParameter(Parameter $parameter): self
    {
        $this->parameters[] = $parameter;

        return $this;
    }

    public function addAlias(string ...$aliases): self
    {
        $this->aliases = $aliases;

        return $this;
    }
}
