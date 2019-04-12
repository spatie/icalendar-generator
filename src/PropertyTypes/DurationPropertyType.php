<?php

namespace Spatie\Calendar\PropertyTypes;

final class DurationPropertyType extends PropertyType
{
    /** @var int */
    protected $minutes;

    public function __construct(string $name, int $minutes)
    {
        $this->name = $name;

        $this->minutes = $minutes;
    }

    public function getValue(): string
    {
        return "PT{$this->minutes}M";
    }

    public function getOriginalValue() : int
    {
        return $this->minutes;
    }
}
