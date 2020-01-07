<?php

namespace Spatie\IcalendarGenerator\PropertyTypes;

class CoordinatesPropertyType extends PropertyType
{
    /** @var float */
    private $lat;

    /** @var float */
    private $lng;

    public static function create($names, float $lat, float $lng): CoordinatesPropertyType
    {
        return new self($names, $lat, $lng);
    }

    /**
     * CoordinatesPropertyType constructor.
     *
     * @param string|array $names
     * @param float $lat
     * @param float $lng
     */
    public function __construct($names, float $lat, float $lng)
    {
        parent::__construct($names);

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
