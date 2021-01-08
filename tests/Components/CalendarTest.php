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
use Spatie\IcalendarGenerator\Tests\TestCase;

class CalendarTest extends TestCase
{
    /** @test */
    public function it_can_create_a_calendar()
    {
        $payload = Calendar::create()->resolvePayload();

        $this->assertEquals('VCALENDAR', $payload->getType());

        $this->assertCount(2, $payload->getProperties());

        $this->assertPropertyEqualsInPayload('VERSION', '2.0', $payload);
        $this->assertPropertyEqualsInPayload('PRODID', 'spatie/icalendar-generator', $payload);
    }

    /** @test */
    public function it_can_set_calendar_properties()
    {
        $payload = Calendar::create()
            ->name('Full Stack Europe Schedule')
            ->description('What events are going to happen?')
            ->productIdentifier('Ruben\'s calendar creator machine')
            ->resolvePayload();

        $this->assertCount(4, $payload->getProperties());

        $this->assertPropertyEqualsInPayload('NAME', 'Full Stack Europe Schedule', $payload);
        $this->assertPropertyEqualsInPayload('X-WR-CALNAME', 'Full Stack Europe Schedule', $payload);
        $this->assertPropertyEqualsInPayload('DESCRIPTION', 'What events are going to happen?', $payload);
        $this->assertPropertyEqualsInPayload('PRODID', 'Ruben\'s calendar creator machine', $payload);
    }

    /** @test */
    public function it_can_add_an_event_to_a_calendar()
    {
        $event = Event::create('An introduction to event sourcing');

        $payload = Calendar::create()
            ->event($event)
            ->withoutAutoTimezoneComponents()
            ->resolvePayload();

        $subComponents = $payload->getSubComponents();

        $this->assertCount(1, $subComponents);
        $this->assertEquals($subComponents[0], $event);
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

        $subComponents = $payload->getSubComponents();

        $this->assertCount(1, $subComponents);
        $this->assertPropertyEqualsInPayload('SUMMARY', 'An introduction to event sourcing', $subComponents[0]->resolvePayload());
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

        $subComponents = $payload->getSubComponents();

        $this->assertCount(2, $subComponents);
        $this->assertEquals($subComponents[0], $firstEvent);
        $this->assertEquals($subComponents[1], $secondEvent);
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

        $subComponents = $payload->getSubComponents();

        $this->assertCount(2, $subComponents);
        $this->assertPropertyEqualsInPayload('SUMMARY', 'An introduction to event sourcing', $subComponents[0]->resolvePayload());
        $this->assertPropertyEqualsInPayload('SUMMARY', 'Websockets what are they?', $subComponents[1]->resolvePayload());
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

        $this->assertPropertyEqualsInPayload('REFRESH-INTERVAL', new DateInterval('PT5M'), $payload);
        $this->assertParameterEqualsInProperty('VALUE', 'DURATION', $payload->getProperty('REFRESH-INTERVAL'));
    }

    /** @test */
    public function it_is_possible_to_add_multiple_events()
    {
        $firstEvent = Event::create('An introduction to event sourcing');
        $secondEvent = Event::create('An introduction to event sourcing');

        $payload = Calendar::create()
            ->event($firstEvent)
            ->event([$secondEvent])
            ->withoutAutoTimezoneComponents()
            ->resolvePayload();

        $subComponents = $payload->getSubComponents();

        $this->assertCount(2, $subComponents);
        $this->assertEquals($subComponents[0], $firstEvent);
        $this->assertEquals($subComponents[1], $secondEvent);
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

        $subComponents = $payload->getSubComponents();

        $this->assertCount(5, $subComponents);

        $this->assertInstanceOf(Timezone::class, $subComponents[0]);
        $this->assertInstanceOf(Timezone::class, $subComponents[1]);
        $this->assertNotInstanceOf(Timezone::class, $subComponents[2]);
        $this->assertNotInstanceOf(Timezone::class, $subComponents[3]);
        $this->assertNotInstanceOf(Timezone::class, $subComponents[4]);

        $this->assertPropertyEqualsInPayload('TZID', 'UTC', $subComponents[0]->resolvePayload());
        $this->assertPropertyEqualsInPayload('TZID', 'Europe/Brussels', $subComponents[1]->resolvePayload());
    }


    /** @test */
    public function it_will_automatically_add_timezone_components()
    {
        Carbon::setTestNow(new CarbonImmutable('1 august 2020'));

        $utcEvent = Event::create('An event with UTC timezone')
            ->startsAt(new CarbonImmutable('1 january 2019'))
            ->endsAt(new CarbonImmutable('1 january 2021'));

        $alternativeTimezoneEvent = Event::create('An event with alternative timezone')
            ->startsAt(new CarbonImmutable('1 january 2020', 'Europe/Brussels'))
            ->endsAt(new CarbonImmutable('1 january 2021', 'Europe/Brussels'));

        $negativeOffsetTimezoneEvent = Event::create('An event with a negative timezone offset')
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
DTSTART:20180406T000000\r
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
DTSTART:20191027T030000\r
TZOFFSETFROM:+0200\r
TZOFFSETTO:+0100\r
END:STANDARD\r
BEGIN:DAYLIGHT\r
DTSTART:20200329T020000\r
TZOFFSETFROM:+0100\r
TZOFFSETTO:+0200\r
END:DAYLIGHT\r
BEGIN:STANDARD\r
DTSTART:20201025T030000\r
TZOFFSETFROM:+0200\r
TZOFFSETTO:+0100\r
END:STANDARD\r
BEGIN:DAYLIGHT\r
DTSTART:20210328T020000\r
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
DTSTART:20191103T020000\r
TZOFFSETFROM:-0400\r
TZOFFSETTO:-0500\r
END:STANDARD\r
BEGIN:DAYLIGHT\r
DTSTART:20200308T020000\r
TZOFFSETFROM:-0500\r
TZOFFSETTO:-0400\r
END:DAYLIGHT\r
BEGIN:STANDARD\r
DTSTART:20201101T020000\r
TZOFFSETFROM:-0400\r
TZOFFSETTO:-0500\r
END:STANDARD\r
BEGIN:DAYLIGHT\r
DTSTART:20210314T020000\r
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

        $subcomponents = $payload->getSubComponents();

        $this->assertCount(3, $subcomponents);
        $this->assertContains($timezoneA, $subcomponents);
        $this->assertContains($timezoneB, $subcomponents);
        $this->assertContains($timezoneC, $subcomponents);
    }
}
