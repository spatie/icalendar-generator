<?php

namespace Spatie\IcalendarGenerator\ValueObjects;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;

class DateTimeValue
{
    private DateTimeInterface $dateTime;

    private bool $withTime;

    public static function create(
        DateTimeInterface $dateTime,
        bool $withTime = true
    ): self {
        return new self($dateTime, $withTime);
    }

    public function __construct(
        DateTimeInterface $dateTime,
        bool $withTime = true
    ) {
        $this->dateTime = $dateTime;
        $this->withTime = $withTime;
    }

    public function format(): string
    {
        // Code was changed to this, let check that out
//        $format = $this->withTime ? 'Ymd\THis' : 'Ymd';
//
//        return $this->dateTime->format($format) .
//            ($this->withTime && ! $this->withTimeZone ? 'Z' : '');

//        $property = new DateTimePropertyType('STARTS', $this->date, true, false);
//          With time is true and timezone false
//        $this->assertEquals('20190516T121015Z', $property->getValue());

//        $this->date->setTimezone(new DateTimeZone('Europe/Brussels'));
//
//        $property = new DateTimePropertyType('STARTS', $this->date, true, true);
//          With time and timezone are both true here
//        $this->assertEquals('20190516T141015', $property->getValue());
//        $this->assertEquals(1, count($property->getParameters()));

        $format = $this->withTime ? 'Ymd\THis' : 'Ymd';

        return $this->dateTime->format($format);
    }

    public function hasTime(): bool
    {
        return $this->withTime;
    }

    public function getDateTime(): DateTimeInterface
    {
        return $this->dateTime;
    }

    public function convertToTimezone(DateTimeZone $dateTimeZone): self
    {
        if (! $this->withTime) {
            return $this;
        }

        $dateTime = $this->dateTime instanceof DateTimeImmutable
            ? DateTime::createFromImmutable($this->dateTime)
            : clone $this->dateTime;

        $this->dateTime = $dateTime->setTimezone($dateTimeZone);

        return $this;
    }

    public function __toString()
    {
        return $this->format();
    }
}
