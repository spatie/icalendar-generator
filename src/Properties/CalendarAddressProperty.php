<?php

namespace Spatie\IcalendarGenerator\Properties;

use Spatie\IcalendarGenerator\ValueObjects\CalendarAddress;

class CalendarAddressProperty extends Property
{
    public static function create(string  $name, CalendarAddress $calendarAddress): CalendarAddressProperty
    {
        return new self($name, $calendarAddress);
    }

    public function __construct(string $name, protected CalendarAddress $calendarAddress)
    {
        $this->name = $name;

        if ($this->calendarAddress->name) {
            $this->addParameter(Parameter::create('CN', $this->calendarAddress->name));
        }

        if ($this->calendarAddress->requiresResponse) {
            $this->addParameter(Parameter::create('RSVP', 'TRUE'));
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
