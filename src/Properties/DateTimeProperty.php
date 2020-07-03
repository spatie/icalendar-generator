<?php

namespace Spatie\IcalendarGenerator\Properties;

use DateTimeInterface;
use Spatie\IcalendarGenerator\ValueObjects\DateTimeValue;

class DateTimeProperty extends Property
{
    private DateTimeInterface $dateTime;

    private bool $withTime;

    private bool $withoutTimeZone;

    public static function create(
        string $name,
        DateTimeInterface $dateTime,
        bool $withTime = false,
        bool $withoutTimeZone = false
    ): DateTimeProperty {
        return new self($name, $dateTime, $withTime, $withoutTimeZone);
    }

    public function __construct(
        string $name,
        DateTimeInterface $dateTime,
        bool $withTime = false,
        bool $withoutTimeZone = false
    ) {
        $this->name = $name;
        $this->dateTime = $dateTime;
        $this->withTime = $withTime;
        $this->withoutTimeZone = $withoutTimeZone;

        if ($this->withTime && $this->withoutTimeZone === false) {
            $timezone = $this->dateTime->getTimezone()->getName();

            $this->addParameter(new Parameter('TZID', $timezone));
        }
    }

    public function getValue(): string
    {
        return new DateTimeValue($this->dateTime, $this->withTime);
    }

    public function getOriginalValue(): DateTimeInterface
    {
        return $this->dateTime;
    }
}
