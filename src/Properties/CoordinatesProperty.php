<?php

namespace Spatie\IcalendarGenerator\Properties;

class CoordinatesProperty extends Property
{
    public static function create(string $name, float $lat, float $lng): CoordinatesProperty
    {
        return new self($name, $lat, $lng);
    }

    public function __construct(string $name, protected float $lat, protected float $lng)
    {
        $this->name = $name;
    }

    public function getValue(): string
    {
        return json_encode($this->lat).';'.json_encode($this->lng);
    }

    /**
     * @return array{lat: float, lng: float}
     */
    public function getOriginalValue(): array
    {
        return [
            'lat' => $this->lat,
            'lng' => $this->lng,
        ];
    }
}
