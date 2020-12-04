<?php

namespace Spatie\IcalendarGenerator\ValueObjects;

use DateInterval;
use Exception;

class DurationValue
{
    private DateInterval $interval;

    public static function create($interval): DurationValue
    {
        if ($interval instanceof DateInterval) {
            return new self($interval);
        }

        if (is_string($interval)) {
            return new self(new DateInterval($interval));
        }

        throw new Exception("A duration can only be a DateInterval");
    }

    private function __construct(DateInterval $interval)
    {
        $this->interval = $interval;
    }

    public function invert(): self
    {
        $this->interval->invert = 1;

        return $this;
    }

    public function format(): string
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
}
