<?php

namespace Spatie\Calendar\Tests\Components;

use Spatie\Calendar\Tests\TestCase;
use Spatie\Calendar\Components\Calendar;
use Spatie\Calendar\Components\Event;
use Spatie\Calendar\PropertyTypes\TextProperty;

class CalendarTest extends TestCase
{
    /** @test */
    public function it_can_create_a_calendar()
    {
        $payload = Calendar::new()->getPayload();

        $properties = $payload->getProperties();

        $this->assertEquals('CALENDAR', $payload->getType());

        $this->assertEquals(2, count($properties));

        $this->assertInArray(new TextProperty('VERSION', '2.0'), $properties);
        $this->assertInArray(new TextProperty('PRODID', 'Spatie/iCalendar-generator'), $properties);
    }

    /** @test */
    public function it_can_set_calendar_properties()
    {
        $payload = Calendar::new()
            ->name('Full Stack Europe Schedule')
            ->description('What events are going to happen?')
            ->getPayload();

        $properties = $payload->getProperties();

        $this->assertEquals(4, count($properties));

        $this->assertInArray(new TextProperty('NAME', 'Full Stack Europe Schedule'), $properties);
        $this->assertInArray(new TextProperty('DESCRIPTION', 'What events are going to happen?'), $properties);
    }

    /** @test */
    public function it_can_add_an_event_to_a_calendar()
    {
        $event = Event::new('An introduction to event sourcing');

        $payload = Calendar::new()
            ->event($event)
            ->getPayload();

        $subComponents = $payload->getSubComponents();

        $this->assertEquals(1, count($subComponents));
        $this->assertEquals($subComponents[0], $event);
    }

    /** @test */
    public function it_can_add_an_event_by_closure_to_a_calendar()
    {
        $payload = Calendar::new()
            ->event(function (Event $event) {
                $event->name('An introduction to event sourcing');
            })
            ->getPayload();

        $subComponents = $payload->getSubComponents();

        $this->assertEquals(1, count($subComponents));

        $eventPayload = $subComponents[0]->getPayload();

        $this->assertInArray(new TextProperty('SUMMARY', 'An introduction to event sourcing'), $eventPayload->getProperties());
    }

    /** @test */
    public function it_can_add_multiple_events_to_a_calendar()
    {
        $firstEvent = Event::new('An introduction to event sourcing');
        $secondEvent = Event::new('Websockets what are they?');

        $payload = Calendar::new()
            ->event([$firstEvent, $secondEvent])
            ->getPayload();

        $subComponents = $payload->getSubComponents();

        $this->assertEquals(2, count($subComponents));
        $this->assertEquals($subComponents[0], $firstEvent);
        $this->assertEquals($subComponents[1], $secondEvent);
    }
}
