<?php

namespace Spatie\IcalendarGenerator\Timezones;

interface HasTimezones
{
    public function getTimezoneRangeCollection(): TimezoneRangeCollection;
}
