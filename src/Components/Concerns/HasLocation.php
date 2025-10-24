<?php

namespace Spatie\IcalendarGenerator\Components\Concerns;

use Spatie\IcalendarGenerator\ComponentPayload;
use Spatie\IcalendarGenerator\Properties\AppleLocationCoordinatesProperty;
use Spatie\IcalendarGenerator\Properties\CoordinatesProperty;
use Spatie\IcalendarGenerator\Properties\TextProperty;

trait HasLocation
{
    protected ?string $address = null;

    protected ?string $addressName = null;

    protected ?float $lat = null;

    protected ?float $lng = null;

    public function address(string $address, ?string $name = null): self
    {
        $this->address = $address;

        if ($name) {
            $this->addressName = $name;
        }

        return $this;
    }

    public function addressName(string $name): self
    {
        $this->addressName = $name;

        return $this;
    }

    public function coordinates(float $lat, float $lng): self
    {
        $this->lat = $lat;
        $this->lng = $lng;

        return $this;
    }

    protected function resolveLocationProperties(ComponentPayload $payload): self
    {
        if ($this->address) {
            $payload->property(TextProperty::create('LOCATION', $this->address));
        }

        if (is_null($this->lng) || is_null($this->lat)) {
            return $this;
        }

        $payload->property(CoordinatesProperty::create('GEO', $this->lat, $this->lng));

        if (is_null($this->address) || is_null($this->addressName)) {
            return $this;
        }

        $property = AppleLocationCoordinatesProperty::create(
            $this->lat,
            $this->lng,
            $this->address,
            $this->addressName
        );

        $payload->property($property);

        return $this;
    }
}
