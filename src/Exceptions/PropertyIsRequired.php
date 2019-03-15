<?php

namespace Spatie\Calendar\Exceptions;

use Exception;
use Spatie\Calendar\Components\Component;

class PropertyIsRequired extends Exception
{
    public static function create(array $properties, Component $component): PropertyIsRequired
    {
        $type = ucfirst(strtolower($component->getComponentType()));

        $properties = implode(', ', $properties);

        return new self("Properties {$properties} is required when creating an {$type}.");
    }
}
