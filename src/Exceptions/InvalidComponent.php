<?php

namespace Spatie\IcalendarGenerator\Exceptions;

use Exception;
use Spatie\IcalendarGenerator\Components\Component;

class InvalidComponent extends Exception
{
    public static function requiredPropertyMissing(array $properties, Component $component): InvalidComponent
    {
        $type = ucfirst(strtolower($component->getComponentType()));

        $properties = implode(', ', $properties);

        return new self("Properties `{$properties}` are required when creating an `{$type}`.");
    }
}
