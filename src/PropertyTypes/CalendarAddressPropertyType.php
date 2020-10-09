<?php

namespace Spatie\IcalendarGenerator\PropertyTypes;

use Spatie\IcalendarGenerator\ValueObjects\CalendarAddress;

class CalendarAddressPropertyType extends PropertyType
{
    private $calendarAddress;

    public static function create($names, CalendarAddress $calendarAddress): CalendarAddressPropertyType
    {
        return new self($names, $calendarAddress);
    }

    /**
     * TextPropertyType constructor.
     *
     * @param array|string $names
     * @param \Spatie\IcalendarGenerator\ValueObjects\CalendarAddress $calendarAddress
     */
    public function __construct($names, CalendarAddress $calendarAddress)
    {
        parent::__construct($names);

        $this->calendarAddress = $calendarAddress;

        if ($this->calendarAddress->name) {
            $this->addParameter(Parameter::create('CN', $this->calendarAddress->name));
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
