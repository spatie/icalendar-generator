<?php

namespace Spatie\Calendar\Exceptions;

use Exception;
use Spatie\Calendar\Components\Component;

final class InvalidComponent extends Exception
{
    public static function requiredPropertyMissing(array $properties, Component $component): InvalidComponent
    {
        $type = ucfirst(strtolower($component->getComponentType()));

        $properties = implode(', ', $properties);

        return new self("Properties `{$properties}` is required when creating an `{$type}`.");
    }
}
