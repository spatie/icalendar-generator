<?php

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertInstanceOf;
use Spatie\IcalendarGenerator\Components\Calendar;
use Spatie\IcalendarGenerator\Components\Event;
use Spatie\IcalendarGenerator\Components\Timezone;

use Spatie\IcalendarGenerator\Tests\PayloadExpectation;
use Spatie\IcalendarGenerator\Tests\PropertyExpectation;

test('it can create a calendar', function () {
    $payload = Calendar::create()->resolvePayload();

    PayloadExpectation::create($payload)
        ->expectType('VCALENDAR')
        ->expectPropertyCount(2)
        ->expectPropertyValue('VERSION', '2.0')
        ->expectPropertyValue('PRODID', 'spatie/icalendar-generator');
});

test('it can set calendar properties', function () {
    $payload = Calendar::create()
        ->name('Full Stack Europe Schedule')
        ->description('What events are going to happen?')
        ->productIdentifier('Ruben\'s calendar creator machine')
        ->resolvePayload();

    PayloadExpectation::create($payload)
        ->expectPropertyCount(4)
        ->expectPropertyValue('NAME', 'Full Stack Europe Schedule')
        ->expectPropertyValue('X-WR-CALNAME', 'Full Stack Europe Schedule')
        ->expectPropertyValue('DESCRIPTION', 'What events are going to happen?')
        ->expectPropertyValue('PRODID', 'Ruben\'s calendar creator machine');
});

test('it can add an event to a calendar', function () {
    $event = Event::create('An introduction to event sourcing');

    $payload = Calendar::create()
        ->event($event)
        ->withoutAutoTimezoneComponents()
        ->resolvePayload();

    PayloadExpectation::create($payload)
        ->expectSubComponentCount(1)
        ->expectSubComponents($event);
});

test('it can add an event by closure to a calendar', function () {
    $payload = Calendar::create()
        ->event(function (Event $event) {
            $event->name('An introduction to event sourcing');
        })
        ->withoutAutoTimezoneComponents()
        ->resolvePayload();

    PayloadExpectation::create($payload)
        ->expectSubComponentCount(1)
        ->expectSubComponent(0, function (PayloadExpectation  $expectation) {
            $expectation->expectPropertyValue('SUMMARY', 'An introduction to event sourcing');
        });
});

test('it can add multiple events to a calendar', function () {
    $firstEvent = Event::create('An introduction to event sourcing');
    $secondEvent = Event::create('Websockets what are they?');

    $payload = Calendar::create()
        ->event([$firstEvent, $secondEvent])
        ->withoutAutoTimezoneComponents()
        ->resolvePayload();

    PayloadExpectation::create($payload)
        ->expectSubComponentCount(2)
        ->expectSubComponents($firstEvent, $secondEvent);
});

test('it can add multiple events by closure to a calendar', function () {
    $payload = Calendar::create()
        ->event([
            function (Event $event) {
                $event->name('An introduction to event sourcing');
            },
            function (Event $event) {
                $event->name('Websockets what are they?');
            },
        ])
        ->withoutAutoTimezoneComponents()
        ->resolvePayload();

    PayloadExpectation::create($payload)
        ->expectSubComponentCount(2)
        ->expectSubComponent(0, function (PayloadExpectation  $expectation) {
            $expectation->expectPropertyValue('SUMMARY', 'An introduction to event sourcing');
        })
        ->expectSubComponent(1, function (PayloadExpectation  $expectation) {
            $expectation->expectPropertyValue('SUMMARY', 'Websockets what are they?');
        });
});

test("when setting with timezone's events will be added with timezones", function () {
    $timezone = new DateTimeZone('Europe/Brussels');
    $date = new DateTime('16 may 2019');

    $date->setTimezone($timezone);

    $payload = Calendar::create()
        ->withoutTimezone()
        ->event(function (Event $event) use ($date) {
            $event->startsAt($date);
        })
        ->resolvePayload();

    $eventTimezone = $payload->getSubComponents()[0]
        ->resolvePayload()
        ->getProperty('DTSTART')
        ->getOriginalValue()
        ->getTimezone();

    assertEquals($timezone, $eventTimezone);
});

test('a refresh rate can be set', function () {
    $payload = Calendar::create()
        ->refreshInterval(5)
        ->resolvePayload();

    PropertyExpectation::create($payload, 'REFRESH-INTERVAL')
        ->expectValue(new DateInterval('PT5M'))
        ->expectParameterValue('VALUE', 'DURATION');
});

test('a source can be set', function () {
    $payload = Calendar::create()
        ->source('https://example.org/cal.ics')
        ->resolvePayload();

    PropertyExpectation::create($payload, 'SOURCE')
        ->expectValue('https://example.org/cal.ics')
        ->expectParameterValue('VALUE', 'URI');
});

test('it will automatically add multiple timezone components', function () {
    Carbon::setTestNow(new CarbonImmutable('1 august 2020'));

    $utcEvent = Event::create('An event with UTC timezone')
        ->startsAt(new CarbonImmutable('1 january 2019'))
        ->endsAt(new CarbonImmutable('1 january 2021'));

    $alternativeTimezoneEvent = Event::create('An event with alternative timezone')
        ->startsAt(new CarbonImmutable('1 january 2020', 'Europe/Brussels'))
        ->endsAt(new CarbonImmutable('1 january 2021', 'Europe/Brussels'));

    $withoutTimezoneEvent = Event::create('An event without timezone')
        ->withoutTimezone()
        ->startsAt(new CarbonImmutable('1 january 1995', 'America/New_York'))
        ->endsAt(new CarbonImmutable('1 january 2021', 'America/New_York'));

    $payload = Calendar::create()->event(
        [$utcEvent, $alternativeTimezoneEvent, $withoutTimezoneEvent]
    )->resolvePayload();

    PayloadExpectation::create($payload)
        ->expectSubComponentCount(5)
        ->expectSubComponent(0, function (PayloadExpectation $expectation) {
            $expectation->expectType('VTIMEZONE')->expectPropertyValue('TZID', 'UTC');
        })
        ->expectSubComponent(1, function (PayloadExpectation $expectation) {
            $expectation->expectType('VTIMEZONE')->expectPropertyValue('TZID', 'Europe/Brussels');
        })
        ->expectSubComponentNotInstanceOf(2, Timezone::class)
        ->expectSubComponentNotInstanceOf(3, Timezone::class)
        ->expectSubComponentNotInstanceOf(4, Timezone::class);
});

test('it will automatically add timezone component', function () {
    Carbon::setTestNow(new CarbonImmutable('1 august 2020'));

    $utcEvent = Event::create('An event with UTC timezone')
        ->createdAt(new CarbonImmutable('1 january 2019'))
        ->startsAt(new CarbonImmutable('1 january 2019'))
        ->endsAt(new CarbonImmutable('1 january 2021'));

    $alternativeTimezoneEvent = Event::create('An event with alternative timezone')
        ->createdAt(new CarbonImmutable('1 january 2020', 'Europe/Brussels'))
        ->startsAt(new CarbonImmutable('1 january 2020', 'Europe/Brussels'))
        ->endsAt(new CarbonImmutable('1 january 2021', 'Europe/Brussels'));

    $negativeOffsetTimezoneEvent = Event::create('An event with a negative timezone offset')
        ->createdAt(new CarbonImmutable('1 january 2020', 'America/New_York'))
        ->startsAt(new CarbonImmutable('1 january 2020', 'America/New_York'))
        ->endsAt(new CarbonImmutable('1 january 2021', 'America/New_York'));

    $payload = Calendar::create()->event(
        [$utcEvent, $alternativeTimezoneEvent, $negativeOffsetTimezoneEvent]
    )->resolvePayload();

    $subComponents = $payload->getSubComponents();

    /** @var \Spatie\IcalendarGenerator\Components\Timezone $utcComponent */
    $utcComponent = $subComponents[0];

    assertInstanceOf(Timezone::class, $utcComponent);
    assertEquals(<<<EOT
BEGIN:VTIMEZONE\r
TZID:UTC\r
BEGIN:STANDARD\r
DTSTART:20180406T000000Z\r
TZOFFSETFROM:+0000\r
TZOFFSETTO:+0000\r
END:STANDARD\r
END:VTIMEZONE
EOT, $utcComponent->toString());

    /** @var \Spatie\IcalendarGenerator\Components\Timezone $alternativeTimezoneComponent */
    $alternativeTimezoneComponent = $subComponents[1];

    assertInstanceOf(Timezone::class, $alternativeTimezoneComponent);
    assertEquals(<<<EOT
BEGIN:VTIMEZONE\r
TZID:Europe/Brussels\r
BEGIN:STANDARD\r
DTSTART:20191027T030000Z\r
TZOFFSETFROM:+0200\r
TZOFFSETTO:+0100\r
END:STANDARD\r
BEGIN:DAYLIGHT\r
DTSTART:20200329T020000Z\r
TZOFFSETFROM:+0100\r
TZOFFSETTO:+0200\r
END:DAYLIGHT\r
BEGIN:STANDARD\r
DTSTART:20201025T030000Z\r
TZOFFSETFROM:+0200\r
TZOFFSETTO:+0100\r
END:STANDARD\r
BEGIN:DAYLIGHT\r
DTSTART:20210328T020000Z\r
TZOFFSETFROM:+0100\r
TZOFFSETTO:+0200\r
END:DAYLIGHT\r
END:VTIMEZONE
EOT, $alternativeTimezoneComponent->toString());

    /** @var \Spatie\IcalendarGenerator\Components\Timezone $negativeOffsetTimezoneComponent */
    $negativeOffsetTimezoneComponent = $subComponents[2];

    assertInstanceOf(Timezone::class, $negativeOffsetTimezoneComponent);
    assertEquals(<<<EOT
BEGIN:VTIMEZONE\r
TZID:America/New_York\r
BEGIN:STANDARD\r
DTSTART:20191103T020000Z\r
TZOFFSETFROM:-0400\r
TZOFFSETTO:-0500\r
END:STANDARD\r
BEGIN:DAYLIGHT\r
DTSTART:20200308T020000Z\r
TZOFFSETFROM:-0500\r
TZOFFSETTO:-0400\r
END:DAYLIGHT\r
BEGIN:STANDARD\r
DTSTART:20201101T020000Z\r
TZOFFSETFROM:-0400\r
TZOFFSETTO:-0500\r
END:STANDARD\r
BEGIN:DAYLIGHT\r
DTSTART:20210314T020000Z\r
TZOFFSETFROM:-0500\r
TZOFFSETTO:-0400\r
END:DAYLIGHT\r
END:VTIMEZONE
EOT, $negativeOffsetTimezoneComponent->toString());
});

test('it can add timezone components manually', function () {
    $timezoneA = Timezone::create('fake/timezone');

    $timezoneB = Timezone::create('fake/timezone');

    $timezoneC = Timezone::create('fake/timezone');

    $payload = Calendar::create()
        ->withoutAutoTimezoneComponents()
        ->timezone($timezoneA)
        ->timezone([$timezoneB, $timezoneC])
        ->timezone(null)
        ->resolvePayload();

    PayloadExpectation::create($payload)
        ->expectSubComponentCount(3)
        ->expectSubComponents($timezoneA, $timezoneB, $timezoneC);
});
