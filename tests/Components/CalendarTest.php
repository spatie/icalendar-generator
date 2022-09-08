<?php

namespace Spatie\IcalendarGenerator\Tests\Components;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use DateInterval;
use DateTime;
use DateTimeZone;
use Spatie\IcalendarGenerator\Components\Calendar;
use Spatie\IcalendarGenerator\Components\Event;
use Spatie\IcalendarGenerator\Components\Timezone;
use Spatie\IcalendarGenerator\Tests\PayloadExpectation;
use Spatie\IcalendarGenerator\Tests\PropertyExpectation;
use Spatie\IcalendarGenerator\Tests\TestCase;

class CalendarTest extends TestCase
{
    /** @test */
    public function it_can_create_a_calendar()
    {
        $payload = Calendar::create()->resolvePayload();

        PayloadExpectation::create($payload)
            ->expectType('VCALENDAR')
            ->expectPropertyCount(2)
            ->expectPropertyValue('VERSION', '2.0')
            ->expectPropertyValue('PRODID', 'spatie/icalendar-generator');
    }

    /** @test */
    public function it_can_set_calendar_properties()
    {
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
    }

    /** @test */
    public function it_can_add_an_event_to_a_calendar()
    {
        $event = Event::create('An introduction to event sourcing');

        $payload = Calendar::create()
            ->event($event)
            ->withoutAutoTimezoneComponents()
            ->resolvePayload();

        PayloadExpectation::create($payload)
            ->expectSubComponentCount(1)
            ->expectSubComponents($event);
    }

    /** @test */
    public function it_can_add_an_event_by_closure_to_a_calendar()
    {
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
    }

    /** @test */
    public function it_can_add_multiple_events_to_a_calendar()
    {
        $firstEvent = Event::create('An introduction to event sourcing');
        $secondEvent = Event::create('Websockets what are they?');

        $payload = Calendar::create()
            ->event([$firstEvent, $secondEvent])
            ->withoutAutoTimezoneComponents()
            ->resolvePayload();

        PayloadExpectation::create($payload)
            ->expectSubComponentCount(2)
            ->expectSubComponents($firstEvent, $secondEvent);
    }

    /** @test */
    public function it_can_add_multiple_events_by_closure_to_a_calendar()
    {
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
    }

    /** @test */
    public function when_setting_with_timezones_events_will_be_added_with_timezones()
    {
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

        $this->assertEquals($timezone, $eventTimezone);
    }

    /** @test */
    public function a_refresh_rate_can_be_set()
    {
        $payload = Calendar::create()
            ->refreshInterval(5)
            ->resolvePayload();

        PropertyExpectation::create($payload, 'REFRESH-INTERVAL')
            ->expectValue(new DateInterval('PT5M'))
            ->expectParameterValue('VALUE', 'DURATION');
    }

    /** @test */
    public function a_source_can_be_set()
    {
        $payload = Calendar::create()
            ->source('https://example.org/cal.ics')
            ->resolvePayload();

        PropertyExpectation::create($payload, 'SOURCE')
            ->expectValue('https://example.org/cal.ics')
            ->expectParameterValue('VALUE', 'URI');
    }

    /** @test */
    public function it_will_automatically_add_multiple_timezone_components()
    {
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
    }

    /** @test */
    public function it_will_automatically_add_timezone_components()
    {
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

        $this->assertInstanceOf(Timezone::class, $utcComponent);
        $this->assertEquals(<<<EOT
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

        $this->assertInstanceOf(Timezone::class, $alternativeTimezoneComponent);
        $this->assertEquals(<<<EOT
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

        $this->assertInstanceOf(Timezone::class, $negativeOffsetTimezoneComponent);
        $this->assertEquals(<<<EOT
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
    }

    /** @test */
    public function it_can_add_timezone_components_manually()
    {
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
    }
}
