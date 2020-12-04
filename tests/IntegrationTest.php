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
use Spatie\Snapshots\MatchesSnapshots;

class IntegrationTest extends TestCase
{
    use MatchesSnapshots;

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
                    ->uniqueIdentifier('uuid_1')
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
                    ->uniqueIdentifier('uuid_2')
                    ->createdAt(new DateTime('6 March 2019 15:00:00'))
                    ->period(new DateTime('6 march 2019 15:00'), new DateTime('7 march 2019 15:00')),
            ])
            ->event(function (Event $event) {
                $event->name('In a timezone')
                    ->uniqueIdentifier('uuid_3')
                    ->createdAt(new DateTime('6 March 2019 16:00:00'))
                    ->startsAt(new DateTime('6 march 2019', new DateTimeZone('Europe/Brussels')))
                    ->url('wrong.uri/is?set=true');
            })
            ->get();

        $this->assertMatchesSnapshot($calendar);
    }
}
