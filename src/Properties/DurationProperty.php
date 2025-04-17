<?php

namespace Spatie\IcalendarGenerator\Properties;

use DateInterval;
use Spatie\IcalendarGenerator\ValueObjects\DurationValue;

class DurationProperty extends Property
{
    public static function create(string $name, DateInterval $interval): DurationProperty
    {
        return new self($name, $interval);
    }

    public function __construct(string $name, protected DateInterval $interval)
    {
        $this->name = $name;
    }

    public function getValue(): string
    {
        return DurationValue::create($this->interval)->format();
    }

    public function getOriginalValue(): DateInterval
    {
        return $this->interval;
    }
}
