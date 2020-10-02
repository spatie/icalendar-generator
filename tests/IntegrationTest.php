<?php

namespace Spatie\IcalendarGenerator\Tests;

use DateTime;
use DateTimeZone;
use Spatie\IcalendarGenerator\Components\Alert;
use Spatie\IcalendarGenerator\Components\Calendar;
use Spatie\IcalendarGenerator\Components\Event;
use Spatie\IcalendarGenerator\Enums\Classification;
use Spatie\IcalendarGenerator\Enums\EventStatus;
use Spatie\IcalendarGenerator\Enums\ParticipationStatus;

class IntegrationTest extends TestCase
{
    /** @test */
    public function it_can_create_a_calendar()
    {
        $calendar = Calendar::create('Laracon online')
            ->refreshInterval(5)
            ->event(
                Event::create()
                    ->startsAt(new DateTime('6 March 2019 15:00:00'))
                    ->endsAt(new DateTime('6 March 2019 16:00:00'))
                    ->createdAt(new DateTime('6 March 2019 16:00:00'))
                    ->description('This description is way too long and should be put onto two different lines in the vcalendar')
                    ->uniqueIdentifier('uuid')
                    ->address('Samberstraat 69D, 2060 Antwerp, Belgium')
                    ->addressName('Spatie HQ')
                    ->coordinates(51.2343, 4.4287)
                    ->url('http://example.com/pub/calendars/jsmith/mytime.ics')
                    ->alertMinutesBefore(5, 'Laracon online is going to start in five mintutes')
                    ->alertMinutesAfter(5, 'Laracon online has ended, see you next year!')
                    ->organizer('ruben@spatie.be', 'Ruben')
                    ->attendee('brent@spatie.be', 'Brent', ParticipationStatus::accepted())
                    ->attendee('alex@spatie.be', 'Alex', ParticipationStatus::declined())
                    ->attendee('freek@spatie.be', 'Freek', ParticipationStatus::tentative())
                    ->transparent()
                    ->classification(Classification::public())
                    ->status(EventStatus::tentative())
                    ->alert(
                        Alert::date(
                            new DateTime('05/16/2020 12:00:00'),
                            'Laracon online has ended, see you next year!'
                        )
                    )
            )
            ->event([
                Event::create('Laracon Online')
                    ->uniqueIdentifier('uuid')
                    ->createdAt(new DateTime('6 March 2019 15:00:00'))
                    ->period(new DateTime('6 march 2019 15:00'), new DateTime('7 march 2019 15:00')),
            ])
            ->event(function (Event $event) {
                $event->name('In a timezone')
                    ->uniqueIdentifier('uuid')
                    ->createdAt(new DateTime('6 March 2019 16:00:00'))
                    ->startsAt(new DateTime('6 march 2019', new DateTimeZone('Europe/Brussels')))
                    ->withTimezone()
                    ->url('wrong.uri/is?set=true');
            })
            ->get();

        $expected = <<<EOD
BEGIN:VCALENDAR\r
VERSION:2.0\r
PRODID:spatie/icalendar-generator\r
NAME:Laracon online\r
X-WR-CALNAME:Laracon online\r
REFRESH-INTERVAL;VALUE=DURATION:PT5M\r
X-PUBLISHED-TTL:PT5M\r
BEGIN:VEVENT\r
UID:uuid\r
DESCRIPTION:This description is way too long and should be put onto two dif\r
 ferent lines in the vcalendar\r
LOCATION:Samberstraat 69D\, 2060 Antwerp\, Belgium\r
CLASS:PUBLIC\r
TRANSP:TRANSPARENT\r
STATUS:TENTATIVE\r
URL:http://example.com/pub/calendars/jsmith/mytime.ics\r
DTSTART:20190306T150000Z\r
DTEND:20190306T160000Z\r
DTSTAMP:20190306T160000Z\r
ORGANIZER;CN=Ruben:MAILTO:ruben@spatie.be\r
ATTENDEE;CN=Brent;PARTSTAT=ACCEPTED:MAILTO:brent@spatie.be\r
ATTENDEE;CN=Alex;PARTSTAT=DECLINED:MAILTO:alex@spatie.be\r
ATTENDEE;CN=Freek;PARTSTAT=TENTATIVE:MAILTO:freek@spatie.be\r
GEO:51.2343;4.4287\r
X-APPLE-STRUCTURED-LOCATION;VALUE=URI;X-ADDRESS=Samberstraat 69D\, 2060 Ant\r
 werp\, Belgium;X-APPLE-RADIUS=72;X-TITLE=Spatie HQ:51.2343;4.4287\r
BEGIN:VALARM\r
ACTION:DISPLAY\r
DESCRIPTION:Laracon online is going to start in five mintutes\r
TRIGGER:-PT5M\r
END:VALARM\r
BEGIN:VALARM\r
ACTION:DISPLAY\r
DESCRIPTION:Laracon online has ended\, see you next year!\r
TRIGGER;RELATED=END:PT5M\r
END:VALARM\r
BEGIN:VALARM\r
ACTION:DISPLAY\r
DESCRIPTION:Laracon online has ended\, see you next year!\r
TRIGGER;VALUE=DATE-TIME:20200516T120000Z\r
END:VALARM\r
END:VEVENT\r
BEGIN:VEVENT\r
UID:uuid\r
SUMMARY:Laracon Online\r
DTSTART:20190306T150000Z\r
DTEND:20190307T150000Z\r
DTSTAMP:20190306T150000Z\r
END:VEVENT\r
BEGIN:VEVENT\r
UID:uuid\r
SUMMARY:In a timezone\r
DTSTART;TZID=Europe/Brussels:20190306T000000\r
DTSTAMP;TZID=UTC:20190306T160000\r
END:VEVENT\r
END:VCALENDAR
EOD;

        $this->assertEquals($expected, $calendar);
    }
}
