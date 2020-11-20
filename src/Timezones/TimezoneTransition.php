<?php

namespace Spatie\IcalendarGenerator\Timezones;

use DateInterval;
use DateTimeInterface;
use Spatie\IcalendarGenerator\Enums\TimezoneEntryType;

class TimezoneTransition
{
    public DateTimeInterface $start;

    public DateInterval $offsetFrom;

    public DateInterval $offsetTo;

    public TimezoneEntryType $type;

    public function __construct(
        DateTimeInterface $start,
        DateInterval $offsetFrom,
        DateInterval $offsetTo,
        TimezoneEntryType $type
    ) {
        $this->start = $start;
        $this->offsetFrom = $offsetFrom;
        $this->offsetTo = $offsetTo;
        $this->type = $type;
    }
}
