<?php

namespace Spatie\IcalendarGenerator\Timezones;

use Spatie\IcalendarGenerator\Timezones\TimezoneRangeCollection;

interface HasTimezones
{
    public function getTimezoneRangeCollection(): TimezoneRangeCollection;
}
