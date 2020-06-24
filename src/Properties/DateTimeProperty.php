<?php

namespace Spatie\IcalendarGenerator\Properties;

use DateTimeInterface;

class DateTimeProperty extends Property
{
    private DateTimeInterface $dateTime;

    private bool $withTime;

    private bool $withTimeZone;

    public static function create(
        string $name,
        DateTimeInterface $dateTime,
        bool $withTime = false,
        bool $withTimeZone = false
    ): DateTimeProperty {
        return new self($name, $dateTime, $withTime, $withTimeZone);
    }

    public function __construct(
        string $name,
        DateTimeInterface $dateTime,
        bool $withTime = false,
        bool $withTimeZone = false
    ) {
        $this->name = $name;
        $this->dateTime = $dateTime;
        $this->withTime = $withTime;
        $this->withTimeZone = $withTimeZone;

        if ($this->withTime && $this->withTimeZone) {
            $timezone = $this->dateTime->getTimezone()->getName();

            $this->addParameter(new Parameter('TZID', $timezone));
        }
    }

    public function getValue(): string
    {
        $format = $this->withTime ? 'Ymd\THis' : 'Ymd';

        return $this->dateTime->format($format);
    }

    public function getOriginalValue(): DateTimeInterface
    {
        return $this->dateTime;
    }
}
