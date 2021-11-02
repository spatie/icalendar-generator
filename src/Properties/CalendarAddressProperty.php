<?php

namespace Spatie\IcalendarGenerator\Properties;

use Spatie\IcalendarGenerator\ValueObjects\CalendarAddress;

class CalendarAddressProperty extends Property
{
    private CalendarAddress $calendarAddress;

    public static function create(string  $name, CalendarAddress $calendarAddress): CalendarAddressProperty
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

        if ($this->calendarAddress->requiresResponse) {
            $this->addParameter(Parameter::create('RSVP', 'TRUE'));
        }

        if ($this->calendarAddress->participationStatus) {
            $this->addParameter(
                Parameter::create('PARTSTAT', (string) $this->calendarAddress->participationStatus)
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
