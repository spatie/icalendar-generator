<?php

namespace Spatie\IcalendarGenerator\Properties;

use DateTimeInterface;
use DateTimeZone;
use Spatie\IcalendarGenerator\ValueObjects\DateTimeValue;

class DateTimeProperty extends Property
{
    protected DateTimeZone $dateTimeZone;

    public static function fromDateTime(
        string $name,
        DateTimeInterface $dateTime,
        bool $withTime = false,
        bool $withTimezone = true
    ): DateTimeProperty {
        return new self($name, new DateTimeValue($dateTime, $withTime, $withTimezone));
    }

    public static function create(
        string $name,
        DateTimeValue $dateTimeValue,
    ): self {
        return new self($name, $dateTimeValue);
    }

    protected function __construct(
        string $name,
        protected DateTimeValue $dateTimeValue,
    ) {
        $this->name = $name;
        $this->dateTimeZone = $dateTimeValue->getDateTime()->getTimezone();

        if ($this->dateTimeValue->withTimezone() && ! $this->isUTC()) {
            $this->addParameter(new Parameter('TZID', $this->dateTimeZone->getName()));
        }

        if (! $dateTimeValue->hasTime()) {
            $this->addParameter(new Parameter('VALUE', 'DATE'));
        }
    }

    public function getValue(): string
    {
        return $this->isUTC() && $this->dateTimeValue->hasTime() && $this->dateTimeValue->withTimezone()
            ? "{$this->dateTimeValue->format()}Z"
            : $this->dateTimeValue->format();
    }

    public function getOriginalValue(): DateTimeInterface
    {
        return $this->dateTimeValue->getDateTime();
    }

    protected function isUTC(): bool
    {
        return $this->dateTimeZone->getName() === 'UTC';
    }
}
