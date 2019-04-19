<?php

namespace Spatie\Calendar\Tests\Components;

use DateTime;
use DateTimeZone;
use Spatie\Calendar\Duration;
use Spatie\Calendar\Tests\TestCase;
use Spatie\Calendar\Components\Event;
use Spatie\Calendar\Components\Calendar;

class CalendarTest extends TestCase
{
    /** @test */
    public function it_can_create_a_calendar()
    {
        $payload = Calendar::create()->getPayload();

        $this->assertEquals('CALENDAR', $payload->getType());

        $this->assertEquals(2, count($payload->getProperties()));

        $this->assertPropertyEqualsInPayload('VERSION', '2.0', $payload);
        $this->assertPropertyEqualsInPayload('PRODID', 'spatie/icalendar-generator', $payload);
    }

    /** @test */
    public function it_can_set_calendar_properties()
    {
        $payload = Calendar::create()
            ->name('Full Stack Europe Schedule')
            ->description('What events are going to happen?')
            ->getPayload();

        $this->assertEquals(4, count($payload->getProperties()));

        $this->assertPropertyEqualsInPayload('NAME', 'Full Stack Europe Schedule', $payload);
        $this->assertAliasEqualsForProperty('NAME', ['X-WR-CALNAME'], $payload);
        $this->assertPropertyEqualsInPayload('DESCRIPTION', 'What events are going to happen?', $payload);
    }

    /** @test */
    public function it_can_add_an_event_to_a_calendar()
    {
        $event = Event::create('An introduction to event sourcing');

        $payload = Calendar::create()
            ->event($event)
            ->getPayload();

        $subComponents = $payload->getSubComponents();

        $this->assertEquals(1, count($subComponents));
        $this->assertEquals($subComponents[0], $event);
    }

    /** @test */
    public function it_can_add_an_event_by_closure_to_a_calendar()
    {
        $payload = Calendar::create()
            ->event(function (Event $event) {
                $event->name('An introduction to event sourcing');
            })
            ->getPayload();

        $subComponents = $payload->getSubComponents();

        $this->assertEquals(1, count($subComponents));
        $this->assertPropertyEqualsInPayload('SUMMARY', 'An introduction to event sourcing', $subComponents[0]->getPayload());
    }

    /** @test */
    public function it_can_add_multiple_events_to_a_calendar()
    {
        $firstEvent = Event::create('An introduction to event sourcing');
        $secondEvent = Event::create('Websockets what are they?');

        $payload = Calendar::create()
            ->event([$firstEvent, $secondEvent])
            ->getPayload();

        $subComponents = $payload->getSubComponents();

        $this->assertEquals(2, count($subComponents));
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
            ->getPayload();

        $subComponents = $payload->getSubComponents();

        $this->assertEquals(2, count($subComponents));
        $this->assertPropertyEqualsInPayload('SUMMARY', 'An introduction to event sourcing', $subComponents[0]->getPayload());
        $this->assertPropertyEqualsInPayload('SUMMARY', 'Websockets what are they?', $subComponents[1]->getPayload());
    }

    /** @test */
    public function when_setting_with_timezones_events_will_be_added_with_timezones()
    {
        $timezone = new DateTimeZone('Europe/Brussels');
        $date = new DateTime('16 may 2019');

        $date->setTimezone($timezone);

        $payload = Calendar::create()
            ->withTimezone()
            ->event(function (Event $event) use ($date) {
                $event->startsAt($date);
            })
            ->getPayload();

        $eventTimezone = $payload->getSubComponents()[0]
            ->getPayload()
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
            ->getPayload();

        $this->assertPropertyEqualsInPayload('REFRESH-INTERVAL', 5, $payload);
        $this->assertParameterEqualsInProperty('VALUE', 'DURATION', $payload->getProperty('REFRESH-INTERVAL'));
    }
}
