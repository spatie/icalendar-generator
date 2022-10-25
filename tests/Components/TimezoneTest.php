<?php

namespace Spatie\IcalendarGenerator\Tests\Components;

use DateTime;
use DateTimeZone;
use Spatie\IcalendarGenerator\Builders\ComponentBuilder;
use Spatie\IcalendarGenerator\Components\Timezone;
use Spatie\IcalendarGenerator\Components\TimezoneEntry;
use Spatie\IcalendarGenerator\Enums\TimezoneEntryType;
use Spatie\IcalendarGenerator\Tests\PayloadExpectation;
use Spatie\IcalendarGenerator\Tests\TestCase;

use function Spatie\Snapshots\assertMatchesSnapshot;

test('it can create a timezone', function () {
    $payload = Timezone::create('Europe/Brussels')->resolvePayload();

    PayloadExpectation::create($payload)
        ->expectPropertyValue('TZID', 'Europe/Brussels');
});

test('it can set a last modified date as UTC', function () {
    $payload = Timezone::create('Europe/Brussels')
        ->lastModified(new DateTime('16 may 2020 12:00:00', new DateTimeZone('Europe/Brussels')))
        ->resolvePayload();

    PayloadExpectation::create($payload)
        ->expectPropertyValue('LAST-MODIFIED', new DateTime('16 may 2020 10:00:00', new DateTimeZone('UTC')));
});

test('it can set an url', function () {
    $payload = Timezone::create('Europe/Brussels')
        ->url('https://spatie.be')
        ->resolvePayload();

    PayloadExpectation::create($payload)
        ->expectPropertyValue('TZURL', 'https://spatie.be');
});

test('it can add timezone entries', function () {
    $payload = Timezone::create('Europe/Brussels')
        ->entry(createTimezoneEntry())
        ->entry([createTimezoneEntry(), createTimezoneEntry()])
        ->entry(null)
        ->entry(createTimezoneEntry())
        ->resolvePayload();

    PayloadExpectation::create($payload)->expectSubComponentCount(4);
});

test('it can write out a timezone', function () {
    $timezone = Timezone::create('Europe/Brussels')
        ->lastModified(new DateTime('16 may 2020 12:00:00', new DateTimeZone('Europe/Brussels')))
        ->url('https://spatie.be')
        ->entry(createTimezoneEntry())
        ->resolvePayload();

    $output = (new ComponentBuilder($timezone))->build();

    assertMatchesSnapshot($output);
});
