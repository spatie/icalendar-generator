<?php

namespace Spatie\Calendar\Tests;

use DateTime;
use PHPUnit\Framework\TestCase;
use Spatie\Calendar\Components\Calendar;
use Spatie\Calendar\Components\Event;

class CalendarTest extends TestCase
{
    /** @test */
    public function it_can_create_a_calendar()
    {
        $payload = Calendar::name('Full Stack Europe Schedule')
            ->toString();

//        $this->assertEquals(
//            'BEGIN:VCALENDAR\r\nVERSION:2.0\r\nPRODID:Full Stack Europe Schedule\r\nEND:VCALENDAR',
//            $payload
//        );
    }

    /** @test */
    public function it_can_create_an_event()
    {
        $payload = Event::name('Websockets')
            ->description('Getting started with websockets')
            ->uuid('identifier')
            ->location('Antwerp')
            ->starts(new DateTime())
            ->ends(new DateTime('+1 hour'))
            ->toString();

//        $this->assertEquals(
//            'BEGIN:VEVENT\r\nUID:identifier\r\nSUMMARY:Websockets\r\nDESCRIPTION:Getting started with websockets\r\nLOCATION:Antwerp\r\nDTSTART:20190306\r\nDTEND:20190306\r\nDTSTAMP:20190306\r\nEND:VEVENT',
//            $payload
//        );
    }

    /** @test */
    public function it_can_add_an_event_to_a_calendar()
    {

        $payload = Calendar::name('Full Stack Europe Schedule')
            ->event(function (Event $event){
                return $event->name('ddd')->uuid('identifier');
            })->toString();

//        $this->assertEquals(
//            'BEGIN:VCALENDAR\r\nVERSION:2.0\r\nPRODID:Full Stack Europe Schedule\r\nEND:VCALENDAR',
//            $payload
//        );
    }
}
