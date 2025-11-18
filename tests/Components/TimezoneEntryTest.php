<?php

use Spatie\IcalendarGenerator\Builders\ComponentBuilder;
use Spatie\IcalendarGenerator\Components\TimezoneEntry;
use Spatie\IcalendarGenerator\Enums\RecurrenceFrequency;
use Spatie\IcalendarGenerator\Enums\TimezoneEntryType;
use Spatie\IcalendarGenerator\Tests\PayloadExpectation;
use Spatie\IcalendarGenerator\ValueObjects\RRule;

use function Spatie\Snapshots\assertMatchesSnapshot;

test('it can create a standard entry', function () {
    $payload = TimezoneEntry::create(
        TimezoneEntryType::Standard,
        new DateTime('16 may 2020 12:00:00'),
        '+00:00',
        '+02:00'
    )->resolvePayload();

    PayloadExpectation::create($payload)
        ->expectType('STANDARD')
        ->expectPropertyValue('DTSTART', new DateTime('16 may 2020 12:00:00'))
        ->expectPropertyValue('TZOFFSETFROM', '+00:00')
        ->expectPropertyValue('TZOFFSETTO', '+02:00');
});

test('it can create a standard entry with negative offsets', function () {
    $payload = TimezoneEntry::create(
        TimezoneEntryType::Standard,
        new DateTime('16 may 2020 12:00:00'),
        '-00:00',
        '-02:00'
    )->resolvePayload();

    PayloadExpectation::create($payload)
        ->expectType('STANDARD')
        ->expectPropertyValue('DTSTART', new DateTime('16 may 2020 12:00:00'))
        ->expectPropertyValue('TZOFFSETFROM', '-00:00')
        ->expectPropertyValue('TZOFFSETTO', '-02:00');
});

test('it can create a daylight entry', function () {
    $payload = TimezoneEntry::create(
        TimezoneEntryType::Daylight,
        new DateTime('16 may 2020 12:00:00'),
        '+00:00',
        '+02:00'
    )->resolvePayload();

    PayloadExpectation::create($payload)
        ->expectType('DAYLIGHT')
        ->expectPropertyValue('DTSTART', new DateTime('16 may 2020 12:00:00'))
        ->expectPropertyValue('TZOFFSETFROM', '+00:00')
        ->expectPropertyValue('TZOFFSETTO', '+02:00');
});

test('it can set a name and description', function () {
    $payload = TimezoneEntry::create(
        TimezoneEntryType::Standard,
        new DateTime('16 may 2020 12:00:00'),
        '+00:00',
        '+02:00'
    )
        ->name('Europe - Brussels')
        ->description('Belgian timezones ftw!')
        ->resolvePayload();

    PayloadExpectation::create($payload)
        ->expectPropertyValue('TZNAME', 'Europe - Brussels')
        ->expectPropertyValue('COMMENT', 'Belgian timezones ftw!');
});

test('it can set a rrule', function () {
    $payload = TimezoneEntry::create(
        TimezoneEntryType::Standard,
        new DateTime('16 may 2020 12:00:00'),
        '+00:00',
        '+02:00'
    )
        ->rrule(RRule::frequency(RecurrenceFrequency::Daily))
        ->resolvePayload();

    PayloadExpectation::create($payload)
        ->expectPropertyValue('RRULE', RRule::frequency(RecurrenceFrequency::Daily));
});

test('it can write out a timezone entry', function () {
    $payload = TimezoneEntry::create(
        TimezoneEntryType::Daylight,
        new DateTime('16 may 2020 12:00:00'),
        '+00:00',
        '+02:00'
    )
        ->rrule(RRule::frequency(RecurrenceFrequency::Daily))
        ->name('Europe - Brussels')
        ->description('Belgian timezones ftw!')
        ->resolvePayload();

    $written = ComponentBuilder::create($payload)->build();

    assertMatchesSnapshot($written);
});

test('it correctly formats half-hour offsets when created from transition', function () {
    $positiveOffsetFrom = new DateInterval('PT9H30M');
    $positiveOffsetFrom->invert = 0;

    $positiveOffsetTo = new DateInterval('PT10H30M');
    $positiveOffsetTo->invert = 0;

    $positiveTransition = new \Spatie\IcalendarGenerator\Timezones\TimezoneTransition(
        new DateTime('2025-10-05 02:00:00'),
        $positiveOffsetFrom,
        $positiveOffsetTo,
        TimezoneEntryType::Daylight
    );

    $positiveEntry = TimezoneEntry::createFromTransition($positiveTransition);
    $positivePayload = $positiveEntry->resolvePayload();

    PayloadExpectation::create($positivePayload)
        ->expectType('DAYLIGHT')
        ->expectPropertyValue('TZOFFSETFROM', '+0930')
        ->expectPropertyValue('TZOFFSETTO', '+1030');

    $negativeOffsetFrom = new DateInterval('PT3H30M');
    $negativeOffsetFrom->invert = 1;

    $negativeOffsetTo = new DateInterval('PT4H30M');
    $negativeOffsetTo->invert = 1;

    $negativeTransition = new \Spatie\IcalendarGenerator\Timezones\TimezoneTransition(
        new DateTime('2025-04-06 03:00:00'),
        $negativeOffsetFrom,
        $negativeOffsetTo,
        TimezoneEntryType::Standard
    );

    $negativeEntry = TimezoneEntry::createFromTransition($negativeTransition);
    $negativePayload = $negativeEntry->resolvePayload();

    PayloadExpectation::create($negativePayload)
        ->expectType('STANDARD')
        ->expectPropertyValue('TZOFFSETFROM', '-0330')
        ->expectPropertyValue('TZOFFSETTO', '-0430');
});

test('it correctly handles Australia Adelaide timezone with half-hour offsets', function () {
    $resolver = new \Spatie\IcalendarGenerator\Timezones\TimezoneTransitionsResolver(
        new DateTimeZone('Australia/Adelaide'),
        new DateTime('2025-08-01 00:00:00'),
        new DateTime('2025-08-31 23:59:59')
    );

    $transitions = $resolver->getTransitions();

    $standardEntry = TimezoneEntry::createFromTransition($transitions[0]);
    $standardPayload = $standardEntry->resolvePayload();

    PayloadExpectation::create($standardPayload)
        ->expectType('STANDARD')
        ->expectPropertyValue('TZOFFSETFROM', '+1030')
        ->expectPropertyValue('TZOFFSETTO', '+0930');

    $daylightEntry = TimezoneEntry::createFromTransition($transitions[1]);
    $daylightPayload = $daylightEntry->resolvePayload();

    PayloadExpectation::create($daylightPayload)
        ->expectType('DAYLIGHT')
        ->expectPropertyValue('TZOFFSETFROM', '+0930')
        ->expectPropertyValue('TZOFFSETTO', '+1030');
});
