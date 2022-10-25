<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
*/

use Spatie\IcalendarGenerator\Components\TimezoneEntry;
use Spatie\IcalendarGenerator\Enums\TimezoneEntryType;

uses(\Spatie\IcalendarGenerator\Tests\TestCase::class)->in('.');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
*/

function createTimezoneEntry(): TimezoneEntry
{
    return TimezoneEntry::create(
        TimezoneEntryType::standard(),
        new DateTime('16 may 2020 12:00:00'),
        '+00:00',
        '+02:00'
    );
}

function createOffset(
    int $hours,
    int $minutes = 0,
    bool $inverted = false
): DateInterval {
    $interval = new DateInterval('PT' . abs($hours) . 'H' . abs($minutes) . 'M');

    $interval->invert = $inverted;

    return $interval;
}
