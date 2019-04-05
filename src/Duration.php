<?php

namespace Spatie\Calendar;

final class Duration
{
    /** @var int */
    private $weeks = 0;

    /** @var int */
    private $days = 0;

    /** @var int */
    private $hours = 0;

    /** @var int */
    private $minutes = 0;

    /** @var int */
    private $seconds = 0;

    /** @var bool */
    private $backInTime = false;

    public static function new(): Duration
    {
        return new self();
    }

    public function weeks(int $weeks): Duration
    {
        $this->weeks = $weeks;

        return $this;
    }

    public function days(int $days): Duration
    {
        $this->days = $days;

        return $this;
    }

    public function hours(int $hours): Duration
    {
        $this->hours = $hours;

        return $this;
    }

    public function minutes(int $minutes): Duration
    {
        $this->minutes = $minutes;

        return $this;
    }

    public function seconds(int $seconds): Duration
    {
        $this->seconds = $seconds;

        return $this;
    }

    public function backInTime(): Duration
    {
        $this->backInTime = true;

        return $this;
    }

    public function build() : string
    {
        $duration = '';

        if ($this->backInTime) {
            $duration .= '-';
        }

        $duration .= 'P';

        if ($this->weeks > 0) {
            return $duration . "{$this->weeks}W";
        }

        if ($this->days > 0) {
            $duration .= "{$this->days}D";
        }

        if ($this->hours > 0 || $this->minutes > 0 || $this->seconds > 0) {
            $duration .= "T{$this->hours}H{$this->minutes}M{$this->seconds}S";
        }

        return $duration;
    }
}
