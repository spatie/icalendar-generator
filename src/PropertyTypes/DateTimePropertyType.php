<?php

namespace Spatie\IcalendarGenerator\PropertyTypes;

use DateTimeInterface;

final class DateTimePropertyType extends PropertyType
{
    /** @var \DateTimeInterface */
    private $dateTime;

    /** @var bool */
    private $withTime;

    /** @var bool */
    private $withTimeZone;

    /**
     * @param array|string $names
     * @param \DateTimeInterface $dateTime
     * @param bool $withTime
     * @param bool $withTimeZone
     *
     * @return \Spatie\IcalendarGenerator\PropertyTypes\DateTimePropertyType
     */
    public static function create(
        $names,
        DateTimeInterface $dateTime,
        bool $withTime = false,
        bool $withTimeZone = false
    ): DateTimePropertyType {
        return new self($names, $dateTime, $withTime, $withTimeZone);
    }

    /**
     * DateTimePropertyType constructor.
     *
     * @param array|string $names
     * @param \DateTimeInterface $dateTime
     * @param bool $withTime
     * @param bool $withTimeZone
     */
    public function __construct(
        $names,
        DateTimeInterface $dateTime,
        bool $withTime = false,
        bool $withTimeZone = false
    ) {
        parent::__construct($names);

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

        return $this->dateTime->format($format) .
            ($this->withTime && ! $this->withTimeZone ? 'Z' : '');
    }

    public function getOriginalValue(): DateTimeInterface
    {
        return $this->dateTime;
    }
}
