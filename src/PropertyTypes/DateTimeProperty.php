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

        if ($this->withTimeZone) {
            $timezone = $this->dateTime->getTimezone()->getName();

            $this->addParameter(new TextProperty('TZID', $timezone));
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
