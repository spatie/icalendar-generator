<?php

namespace Spatie\IcalendarGenerator\PropertyTypes;

use Spatie\IcalendarGenerator\ValueObjects\CalendarAddress;

class CalendarAddressPropertyType extends PropertyType
{
    private CalendarAddress $calendarAddress;

    public static function create(string  $name, CalendarAddress $calendarAddress): CalendarAddressPropertyType
    {
        return new self($name, $calendarAddress);
    }

    public function __construct(string $name, CalendarAddress $calendarAddress)
    {
        $this->name = $name;
        $this->calendarAddress = $calendarAddress;

        if ($this->calendarAddress->name) {
            $this->addParameter(Parameter::create('CN', $this->calendarAddress->name));
        }

        if ($this->calendarAddress->participationStatus) {
            $this->addParameter(
                Parameter::create('PARTSTAT', $this->calendarAddress->participationStatus)
            );
        }
    }

    public function getValue(): string
    {
        return "MAILTO:{$this->calendarAddress->email}";
    }

    public function getOriginalValue(): CalendarAddress
    {
        return $this->calendarAddress;
    }
}
