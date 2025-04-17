<?php

namespace Spatie\IcalendarGenerator\Properties;

use Exception;

abstract class Property
{
    protected string $name;

    /** @var array<Parameter> */
    protected array $parameters = [];

    /** @var array<string> */
    protected array $aliases = [];

    abstract public function getValue(): ?string;

    abstract public function getOriginalValue(): mixed;

    /** @return array<string> */
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

    /** @return array<string> */
    public function getAliases(): array
    {
        return $this->aliases;
    }

    /** @return array<Parameter> */
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

    /** @param array<Parameter> $parameters */
    public function addParameters(array $parameters): static
    {
        foreach ($parameters as $parameter) {
            $this->addParameter($parameter);
        }

        return $this;
    }

    public function addParameter(Parameter $parameter): static
    {
        $this->parameters[] = $parameter;

        return $this;
    }

    public function addAlias(string ...$aliases): static
    {
        $this->aliases = $aliases;

        return $this;
    }
}
