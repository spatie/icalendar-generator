<?php

namespace Spatie\Calendar\Tests\Components;

use Spatie\Calendar\Tests\TestCase;
use Spatie\Calendar\Components\Calendar;
use Spatie\Calendar\Components\Event;
use Spatie\Calendar\PropertyTypes\TextPropertyType;

class CalendarTest extends TestCase
{
    /** @test */
    public function it_can_create_a_calendar()
    {
        $payload = Calendar::new()->getPayload();

        $this->assertEquals('CALENDAR', $payload->getType());

        $this->assertEquals(2, count($payload->getProperties()));

        $this->assertPropertyEqualsInPayload('VERSION', '2.0', $payload);
        $this->assertPropertyEqualsInPayload('PRODID', 'Spatie/iCalendar-generator', $payload);
    }

    /** @test */
    public function it_can_set_calendar_properties()
    {
        $payload = Calendar::new()
            ->name('Full Stack Europe Schedule')
            ->description('What events are going to happen?')
            ->getPayload();

        $this->assertEquals(4, count($payload->getProperties()));

        $this->assertPropertyEqualsInPayload('NAME', 'Full Stack Europe Schedule', $payload);
        $this->assertPropertyEqualsInPayload('DESCRIPTION', 'What events are going to happen?', $payload);
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
        $this->assertPropertyEqualsInPayload('SUMMARY', 'An introduction to event sourcing', $subComponents[0]->getPayload());
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

    /** @test */
    public function it_can_add_multiple_events_by_closure_to_a_calendar()
    {
        $payload = Calendar::new()
            ->event([
                function (Event $event) {
                    $event->name('An introduction to event sourcing');
                },
                function (Event $event) {
                    $event->name('Websockets what are they?');
                }
            ])
            ->getPayload();

        $subComponents = $payload->getSubComponents();

        $this->assertEquals(2, count($subComponents));
        $this->assertPropertyEqualsInPayload('SUMMARY', 'An introduction to event sourcing', $subComponents[0]->getPayload());
        $this->assertPropertyEqualsInPayload('SUMMARY', 'Websockets what are they?', $subComponents[1]->getPayload());
    }
}
