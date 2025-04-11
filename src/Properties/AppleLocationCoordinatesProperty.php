<?php

namespace Spatie\IcalendarGenerator\Properties;

class AppleLocationCoordinatesProperty extends Property
{
    public static function create(float $lat, float $lng, string $address, string $addressName, int $radius = 72): AppleLocationCoordinatesProperty
    {
        return new self($lat, $lng, $address, $addressName, $radius);
    }

    public function __construct(
        protected float $lat,
        protected float $lng,
        string $address,
        string $addressName,
        int $radius
    ) {
        $this->name = 'X-APPLE-STRUCTURED-LOCATION';

        $this->addParameter(Parameter::create('VALUE', 'URI'));
        $this->addParameter(Parameter::create('X-ADDRESS', $address));
        $this->addParameter(Parameter::create('X-APPLE-RADIUS', $radius));
        $this->addParameter(Parameter::create('X-TITLE', $addressName));
    }

    public function getValue(): string
    {
        return "geo:{$this->lat},{$this->lng}";
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
