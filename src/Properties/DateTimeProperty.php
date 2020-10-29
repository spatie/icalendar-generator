<?php

namespace Spatie\IcalendarGenerator\Properties;

use DateTimeInterface;
use Spatie\IcalendarGenerator\ValueObjects\DateTimeValue;

class DateTimeProperty extends Property
{
    private DateTimeValue $dateTimeValue;

    public static function fromDateTime(
        string $name,
        DateTimeInterface $dateTime,
        bool $withTime = false,
        bool $withoutTimeZone = false
    ): DateTimeProperty {
        return new self($name, new DateTimeValue($dateTime, $withTime), $withoutTimeZone);
    }

    public static function create(
        string $name,
        DateTimeValue $dateTimeValue,
        bool $withoutTimeZone = false
    ) {
        return new self($name, $dateTimeValue, $withoutTimeZone);
    }

    private function __construct(
        string $name,
        DateTimeValue $dateTimeValue,
        bool $withoutTimeZone = false
    ) {
        $this->name = $name;
        $this->dateTimeValue = $dateTimeValue;

        if ($dateTimeValue->hasTime() && $withoutTimeZone === false) {
            $timezone = $dateTimeValue->getDateTime()->getTimezone()->getName();

            $this->addParameter(new Parameter('TZID', $timezone));
        }
    }

    public function getValue(): string
    {
        return $this->dateTimeValue->format();
    }

    public function getOriginalValue(): DateTimeInterface
    {
        return $this->dateTimeValue->getDateTime();
    }
}
