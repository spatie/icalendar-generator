<?php

namespace Spatie\Calendar\PropertyTypes;

use DateTime;
use DateTimeInterface;

class DateTimeProperty extends Property
{
    /** @var \DateTimeImmutable */
    protected $dateTime;

    /** @var bool */
    protected $withTime;

    /** @var bool */
    protected $withTimeZone;

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
    }

    public function getParameters(): array
    {
        if(! $this->withTimeZone){
            return [];
        }

        $timezone = $this->dateTime->getTimezone()->getName();

        return [
            new TextProperty('TZID', $timezone),
        ];
    }

    public function getValue(): string
    {
        $format = $this->withTime ? 'Ymd\THis' : 'Ymd';

        return $this->dateTime->format($format);
    }

    public function getOriginalValue() : DateTime
    {
        return $this->dateTime;
    }
}
