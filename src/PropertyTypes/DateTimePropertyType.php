<?php

namespace Spatie\Calendar\PropertyTypes;

use DateTime;
use DateTimeInterface;

final class DateTimePropertyType extends PropertyType
{
    /** @var \DateTimeInterface */
    private $dateTime;

    /** @var bool */
    private $withTime;

    /** @var bool */
    private $withTimeZone;

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
