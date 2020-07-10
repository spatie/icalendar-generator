<?php

namespace Spatie\IcalendarGenerator\ValueObjects;

use DateTimeInterface;

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

    public function __toString()
    {
        return $this->format();
    }
}
