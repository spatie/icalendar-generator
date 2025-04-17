<?php

namespace Spatie\IcalendarGenerator\Timezones;

use DateInterval;
use DateTimeInterface;
use Spatie\IcalendarGenerator\Enums\TimezoneEntryType;

class TimezoneTransition
{
    public function __construct(
        public DateTimeInterface $start,
        public DateInterval $offsetFrom,
        public DateInterval $offsetTo,
        public TimezoneEntryType $type
    ) {
    }
}
