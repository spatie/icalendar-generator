<?php

namespace Spatie\Calendar\Components;

use ReflectionClass;
use ReflectionObject;
use Spatie\Calendar\Builders\ComponentBuilder;
use Spatie\Calendar\ComponentPayload;
use Spatie\Calendar\Exceptions\PropertyIsRequired;

abstract class Component
{
    abstract public function getComponentType(): string;

    abstract public function getRequiredProperties(): array;

    abstract public function getPayload(): ComponentPayload;

    public function toString(): string
    {
        $builder = new ComponentBuilder($this->getPayload());

        return $builder->build();
    }

    public function ensureRequiredPropertiesAreSet()
    {
        foreach ($this->getRequiredProperties() as $requiredProperty) {
            if ($this->$requiredProperty === null) {
                throw PropertyIsRequired::create($requiredProperty, $this);
            }
        }
    }
}
