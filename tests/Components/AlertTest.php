<?php

use Spatie\IcalendarGenerator\Components\Alert;
use Spatie\IcalendarGenerator\Tests\PayloadExpectation;
use Spatie\IcalendarGenerator\Tests\PropertyExpectation;

test('it can create an alert at a date', function () {
    $trigger = new DateTime('16 may 2019');

    $payload = (new Alert('It is time'))->triggerDate($trigger)->resolvePayload();

    PayloadExpectation::create($payload)
        ->expectType('VALARM')
        ->expectPropertyCount(3)
        ->expectPropertyValue('ACTION', 'DISPLAY')
        ->expectPropertyValue('DESCRIPTION', 'It is time')
        ->expectProperty('TRIGGER', function (PropertyExpectation  $expectation) use ($trigger) {
            $expectation
                ->expectValue($trigger)
                ->expectParameterCount(1)
                ->expectParameterValue('VALUE', 'DATE-TIME');
        });
});

test('it can create an alert without timezone at a date', function () {
    $trigger = new DateTime('16 may 2019');

    $payload = (new Alert('It is time'))
        ->withoutTimezone()
        ->triggerDate($trigger)
        ->resolvePayload();

    PropertyExpectation::create($payload, 'TRIGGER')
        ->expectParameterCount(1)
        ->expectParameterValue('VALUE', 'DATE-TIME');
});

test('it can create an alert at the start of an event', function () {
    $trigger = new DateInterval('PT5M');

    $payload = (new Alert())
        ->triggerAtStart($trigger)
        ->resolvePayload();

    PayloadExpectation::create($payload)
        ->expectType('VALARM')
        ->expectPropertyCount(2)
        ->expectPropertyValue('ACTION', 'DISPLAY')
        ->expectProperty('TRIGGER', function (PropertyExpectation $expectation) use ($trigger) {
            $expectation->expectValue($trigger)->expectParameterCount(0);
        });
});

test('it can create an alert at the end of an event', function () {
    $trigger = new DateInterval('PT5M');

    $payload = (new Alert())
        ->triggerAtEnd($trigger)
        ->resolvePayload();

    PayloadExpectation::create($payload)
        ->expectType('VALARM')
        ->expectPropertyCount(2)
        ->expectPropertyValue('ACTION', 'DISPLAY')
        ->expectProperty('TRIGGER', function (PropertyExpectation $expectation) use ($trigger) {
            $expectation->expectValue($trigger)
                ->expectParameterCount(1)
                ->expectParameterValue('RELATED', 'END');
        });
});

test('it can create constructed as static before or after', function () {
    $interval = new DateInterval('PT5M');

    $payload = Alert::minutesBeforeStart(5)->resolvePayload();
    $interval->invert = 1;

    PropertyExpectation::create($payload, 'TRIGGER')
        ->expectValue($interval)
        ->expectParameterCount(0);

    $payload = Alert::minutesAfterStart(5)->resolvePayload();
    $interval->invert = 0;

    PropertyExpectation::create($payload, 'TRIGGER')
        ->expectValue($interval)
        ->expectParameterCount(0);

    $payload = Alert::minutesBeforeEnd(5)->resolvePayload();
    $interval->invert = 1;

    PropertyExpectation::create($payload, 'TRIGGER')
        ->expectValue($interval)
        ->expectParameterCount(1)
        ->expectParameterValue('RELATED', 'END');

    $payload = Alert::minutesAfterEnd(5)->resolvePayload();
    $interval->invert = 0;

    PropertyExpectation::create($payload, 'TRIGGER')
        ->expectValue($interval)
        ->expectParameterCount(1)
        ->expectParameterValue('RELATED', 'END');
});
