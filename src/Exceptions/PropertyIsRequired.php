<?php

namespace Spatie\Calendar\Exceptions;

use Exception;
use Spatie\Calendar\Components\Component;

class PropertyIsRequired extends Exception
{
    public static function create(string $property, Component $component): PropertyIsRequired
    {
        $type = ucfirst(strtolower($component->getComponentType()));

        return new self("Property {$property} is required when creating an {$type}.");
    }
}
