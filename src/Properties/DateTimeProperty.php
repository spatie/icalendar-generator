<?php

namespace Spatie\IcalendarGenerator\Properties;

use DateTimeInterface;
use DateTimeZone;
use Spatie\IcalendarGenerator\ValueObjects\DateTimeValue;

class DateTimeProperty extends Property
{
    private DateTimeValue $dateTimeValue;

    private DateTimeZone $dateTimeZone;

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
        $this->dateTimeZone = $dateTimeValue->getDateTime()->getTimezone();

        if (! $withoutTimeZone && ! $this->isUTC()) {
            $this->addParameter(new Parameter('TZID', $this->dateTimeZone->getName()));
        }

        if (! $dateTimeValue->hasTime()) {
            $this->addParameter(new Parameter('VALUE', 'DATE'));
        }
    }

    public function getValue(): string
    {
        return $this->isUTC() && $this->dateTimeValue->hasTime()
            ? "{$this->dateTimeValue->format()}Z"
            : $this->dateTimeValue->format();
    }

    public function getOriginalValue(): DateTimeInterface
    {
        return $this->dateTimeValue->getDateTime();
    }

    private function isUTC(): bool
    {
        return $this->dateTimeZone->getName() === 'UTC';
    }
}
