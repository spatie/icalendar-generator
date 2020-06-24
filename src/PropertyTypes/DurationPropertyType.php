<?php

namespace Spatie\IcalendarGenerator\PropertyTypes;

use DateInterval;

final class DurationPropertyType extends PropertyType
{
    private DateInterval $interval;

    public static function create(string $name, DateInterval $interval): DurationPropertyType
    {
        return new self($name, $interval);
    }

    public function __construct(string $name, DateInterval $interval)
    {
        $this->name = $name;
        $this->interval = $interval;
    }

    public function invert(): DurationPropertyType
    {
        $this->interval->invert = 1;

        return $this;
    }

    public function getValue(): string
    {
        $value = $this->interval->invert ? '-P' : 'P';

        if ($this->interval->d > 0) {
            $value .= "{$this->interval->d}D";
        }

        if ($this->interval->s > 0 || $this->interval->i > 0 || $this->interval->h > 0) {
            $value .= 'T';
        }

        if ($this->interval->h > 0) {
            $value .= "{$this->interval->h}H";
        }

        if ($this->interval->i > 0) {
            $value .= "{$this->interval->i}M";
        }

        if ($this->interval->s > 0) {
            $value .= "{$this->interval->s}S";
        }

        return $value;
    }

    public function getOriginalValue(): DateInterval
    {
        return $this->interval;
    }
}
