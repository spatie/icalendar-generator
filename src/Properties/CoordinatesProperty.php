<?php

namespace Spatie\IcalendarGenerator\Properties;

class CoordinatesProperty extends Property
{
    private float $lat;

    private float $lng;

    public static function create(string $name, float $lat, float $lng): CoordinatesProperty
    {
        return new self($name, $lat, $lng);
    }

    public function __construct(string $name, float $lat, float $lng)
    {
        $this->name = $name;
        $this->lat = $lat;
        $this->lng = $lng;
    }

    public function getValue(): string
    {
        return "{$this->lat};{$this->lng}";
    }

    public function getOriginalValue(): array
    {
        return [
            'lat' => $this->lat,
            'lng' => $this->lng,
        ];
    }
}
