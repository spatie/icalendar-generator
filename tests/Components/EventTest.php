<?php

namespace Spatie\IcalendarGenerator\Tests\Components;

use DateTime;
use DateTimeZone;
use Spatie\IcalendarGenerator\Components\Alert;
use Spatie\IcalendarGenerator\Components\Event;
use Spatie\IcalendarGenerator\Enums\Classification;
use Spatie\IcalendarGenerator\Enums\EventStatus;
use Spatie\IcalendarGenerator\Enums\ParticipationStatus;
use Spatie\IcalendarGenerator\Enums\RecurrenceFrequency;
use Spatie\IcalendarGenerator\Properties\DateTimeProperty;
use Spatie\IcalendarGenerator\Tests\PayloadExpectation;
use Spatie\IcalendarGenerator\Tests\PropertyExpectation;
use Spatie\IcalendarGenerator\Tests\TestCase;
use Spatie\IcalendarGenerator\ValueObjects\CalendarAddress;
use Spatie\IcalendarGenerator\ValueObjects\RRule;

class EventTest extends TestCase
{
    /** @test */
    public function it_can_create_an_event()
    {
        $payload = Event::create()->resolvePayload();

        PayloadExpectation::create($payload)
            ->expectType('VEVENT')
            ->expectPropertyCount(2)
            ->expectPropertyExists('UID')
            ->expectPropertyExists('DTSTAMP');
    }

    /** @test */
    public function it_can_set_properties_on_an_event()
    {
        $dateCreated = new DateTime('16 may 2019');
        $dateStarts = new DateTime('17 may 2019');
        $dateEnds = new DateTime('18 may 2019');

        $payload = Event::create('An introduction into event sourcing')
            ->description('By Freek Murze')
            ->createdAt($dateCreated)
            ->url('http://example.com/pub/calendars/jsmith/mytime.ics')
            ->uniqueIdentifier('Identifier here')
            ->startsAt($dateStarts)
            ->endsAt($dateEnds)
            ->address('Antwerp')
            ->addressName('Spatie')
            ->resolvePayload();

        PayloadExpectation::create($payload)
            ->expectPropertyCount(8)
            ->expectPropertyValue('SUMMARY', 'An introduction into event sourcing')
            ->expectPropertyValue('DESCRIPTION', 'By Freek Murze')
            ->expectPropertyValue('DTSTAMP', $dateCreated)
            ->expectPropertyValue('DTSTART', $dateStarts)
            ->expectPropertyValue('DTEND', $dateEnds)
            ->expectPropertyValue('LOCATION', 'Antwerp')
            ->expectPropertyValue('UID', 'Identifier here')
            ->expectPropertyValue('URL', 'http://example.com/pub/calendars/jsmith/mytime.ics');
    }

    /** @test */
    public function it_can_set_a_period_on_an_event()
    {
        $dateStarts = new DateTime('17 may 2019');
        $dateEnds = new DateTime('18 may 2019');

        $payload = Event::create('An introduction into event sourcing')
            ->period($dateStarts, $dateEnds)
            ->resolvePayload();

        PayloadExpectation::create($payload)
            ->expectPropertyValue('DTSTART', $dateStarts)
            ->expectPropertyValue('DTEND', $dateEnds);
    }

    /** @test */
    public function an_event_can_be_a_full_day()
    {
        $dateStarts = new DateTime('17 may 2019');
        $dateEnds = new DateTime('18 may 2019');

        $payload = Event::create('An introduction into event sourcing')
            ->fullDay()
            ->period($dateStarts, $dateEnds)
            ->resolvePayload();

        PropertyExpectation::create($payload, 'DTSTART')
            ->expectParameterCount(1)
            ->expectParameterValue('VALUE', 'DATE');

        PropertyExpectation::create($payload, 'DTEND')
            ->expectParameterCount(1)
            ->expectParameterValue('VALUE', 'DATE');
    }

    /** @test */
    public function an_event_can_be_a_full_day_with_timezones()
    {
        $dateStarts = new DateTime('17 may 2019', new DateTimeZone('Europe/London'));
        $dateEnds = new DateTime('18 may 2019', new DateTimeZone('Europe/London'));

        $payload = Event::create('An introduction into event sourcing')
            ->fullDay()
            ->period($dateStarts, $dateEnds)
            ->resolvePayload();

        PropertyExpectation::create($payload, 'DTSTART')
            ->expectParameterCount(2)
            ->expectParameterValue('VALUE', 'DATE')
            ->expectParameterValue('TZID', 'Europe/London');

        PropertyExpectation::create($payload, 'DTEND')
            ->expectParameterCount(2)
            ->expectParameterValue('VALUE', 'DATE')
            ->expectParameterValue('TZID', 'Europe/London');
    }

    /** @test */
    public function an_event_can_be_a_full_day_without_specifying_an_end()
    {
        $dateStarts = new DateTime('17 may 2019');

        $payload = Event::create('An introduction into event sourcing')
            ->fullDay()
            ->startsAt($dateStarts)
            ->resolvePayload();

        PayloadExpectation::create($payload)
            ->expectPropertyMissing('DTEND')
            ->expectProperty('DTSTART', function (PropertyExpectation $expectation) {
                $expectation
                    ->expectParameterCount(1)
                    ->expectParameterValue('VALUE', 'DATE');
            });
    }

    /** @test */
    public function it_can_alert_minutes_before_an_event()
    {
        $payload = Event::create()
            ->alertMinutesBefore(5)
            ->resolvePayload();

        PayloadExpectation::create($payload)
            ->expectSubComponentCount(1)
            ->expectSubComponentInstanceOf(0, Alert::class);
    }

    /** @test */
    public function it_can_alert_minutes_after_an_event()
    {
        $payload = Event::create()
            ->alertMinutesAfter(5)
            ->resolvePayload();

        PayloadExpectation::create($payload)
            ->expectSubComponentCount(1)
            ->expectSubComponentInstanceOf(0, Alert::class);
    }

    /** @test */
    public function it_can_add_an_alert()
    {
        $payload = Event::create()
            ->alert(new Alert('Test'))
            ->resolvePayload();

        PayloadExpectation::create($payload)
            ->expectSubComponentCount(1)
            ->expectSubComponentInstanceOf(0, Alert::class);
    }

    /** @test */
    public function it_can_set_the_coordinates()
    {
        $payload = Event::create('An introduction into event sourcing')
            ->coordinates(51.2343, 4.4287)
            ->resolvePayload();

        PropertyExpectation::create($payload, 'GEO')
            ->expectValue(['lat' => 51.2343, 'lng' => 4.4287]);
    }

    /** @test */
    public function it_can_generate_an_apple_structured_location()
    {
        $payload = Event::create('An introduction into event sourcing')
            ->coordinates(51.2343, 4.4287)
            ->address('Samberstraat 69D, 2060 Antwerpen, Belgium')
            ->addressName('Spatie HQ')
            ->resolvePayload();

        PropertyExpectation::create($payload, 'X-APPLE-STRUCTURED-LOCATION')
            ->expectValue(['lat' => 51.2343, 'lng' => 4.4287,])
            ->expectOutput('geo:51.2343,4.4287')
            ->expectParameterValue('VALUE', 'URI')
            ->expectParameterValue('X-ADDRESS', 'Samberstraat 69D\, 2060 Antwerpen\, Belgium')
            ->expectParameterValue('X-APPLE-RADIUS', 72)
            ->expectParameterValue('X-TITLE', 'Spatie HQ');
    }

    /** @test */
    public function it_can_add_a_classification()
    {
        $payload = Event::create()
            ->classification(Classification::private())
            ->resolvePayload();

        PropertyExpectation::create($payload, 'CLASS')
            ->expectValue(Classification::private()->value)
            ->expectOutput(Classification::private()->value);
    }

    /** @test */
    public function it_can_make_an_event_transparent()
    {
        $payload = Event::create()
            ->transparent()
            ->resolvePayload();

        PropertyExpectation::create($payload, 'TRANSP')
            ->expectValue('TRANSPARENT');
    }

    /** @test */
    public function it_can_add_an_organizer()
    {
        $payload = Event::create()
            ->organizer('ruben@spatie.be', 'Ruben')
            ->resolvePayload();

        PropertyExpectation::create($payload, 'ORGANIZER')
            ->expectValue(new CalendarAddress('ruben@spatie.be', 'Ruben'));
    }

    /** @test */
    public function it_can_add_attendees()
    {
        $payload = Event::create()
            ->attendee('ruben@spatie.be')
            ->attendee('brent@spatie.be', 'Brent')
            ->attendee('adriaan@spatie.be', 'Adriaan', ParticipationStatus::declined())
            ->resolvePayload();

        PayloadExpectation::create($payload)->expectPropertyValue(
            'ATTENDEE',
            new CalendarAddress('ruben@spatie.be'),
            new CalendarAddress('brent@spatie.be', 'Brent'),
            new CalendarAddress('adriaan@spatie.be', 'Adriaan', ParticipationStatus::declined())
        );
    }

    /** @test */
    public function it_can_set_a_status()
    {
        $payload = Event::create()
            ->status(EventStatus::tentative())
            ->resolvePayload();

        PropertyExpectation::create($payload, 'STATUS')
            ->expectValue(EventStatus::tentative()->value);
    }

    /** @test */
    public function it_can_set_an_address_without_name()
    {
        $dateStarts = new DateTime('17 may 2019');
        $dateEnds = new DateTime('18 may 2019');

        $payload = Event::create('An introduction into event sourcing')
            ->startsAt($dateStarts)
            ->endsAt($dateEnds)
            ->address('Antwerp')
            ->resolvePayload();

        PropertyExpectation::create($payload, 'LOCATION')
            ->expectValue('Antwerp');
    }

    /** @test */
    public function it_can_set_an_recurrence_rule()
    {
        $payload = Event::create('An introduction into event sourcing')
            ->rrule($rrule = RRule::frequency(RecurrenceFrequency::daily()))
            ->resolvePayload();

        PropertyExpectation::create($payload, 'RRULE')
            ->expectValue($rrule);
    }

    /** @test */
    public function it_can_create_an_event_without_timezones()
    {
        $dateAlert = new DateTime('17 may 2019 11:00:00');
        $dateStarts = new DateTime('17 may 2019 12:00:00');
        $dateEnds = new DateTime('18 may 2019 13:00:00');

        $payload = Event::create('An introduction into event sourcing')
            ->withoutTimezone()
            ->alertAt($dateAlert)
            ->startsAt($dateStarts)
            ->endsAt($dateEnds)
            ->resolvePayload();

        PropertyExpectation::create($payload, 'DTSTART')->expectParameterCount(0);
        PropertyExpectation::create($payload, 'DTEND')->expectParameterCount(0);
        PropertyExpectation::create($payload, 'DTSTAMP')->expectParameterCount(0);

        PayloadExpectation::create($payload)->expectSubComponent(0, function (PayloadExpectation $expectation) {
            $expectation->expectProperty('TRIGGER', function (PropertyExpectation $expectation) {
                $expectation
                    ->expectParameterCount(1)
                    ->expectParameterValue('VALUE', 'DATE-TIME');
            });
        });
    }

    /** @test */
    public function it_can_set_a_url()
    {
        $payload = Event::create()
            ->url('http://example.com/pub/calendars/jsmith/mytime.ics')
            ->resolvePayload();

        PayloadExpectation::create($payload)
            ->expectPropertyValue('URL', 'http://example.com/pub/calendars/jsmith/mytime.ics');
    }

    /** @test */
    public function it_ignores_a_wrong_url()
    {
        $payload = Event::create()
            ->url('xample.com/pub/calendars/jsmith/mytime.ics')
            ->resolvePayload();

        PayloadExpectation::create($payload)
            ->expectPropertyMissing('URL');
    }

    /** @test */
    public function it_will_always_use_utc_for_a_created_date_stamp()
    {
        $created = new DateTime('16 may 2020 12:00:00', new DateTimeZone('Europe/Brussels'));

        $payload = Event::create()
            ->createdAt($created)
            ->resolvePayload();

        PayloadExpectation::create($payload)
            ->expectPropertyValue('DTSTAMP', new DateTime('16 may 2020 10:00:00', new DateTimeZone('UTC')));
    }

    /** @test */
    public function it_can_add_recurrence_dates()
    {
        PropertyExpectation::create(
            Event::create()->repeatOn(new DateTime('16 may 2020 12:00:00'))->resolvePayload(),
            'RDATE'
        )->expectBuilt('RDATE;VALUE=DATE-TIME:20200516T120000Z');

        PropertyExpectation::create(
            Event::create()->repeatOn(new DateTime('16 may 2020 12:00:00'), false)->resolvePayload(),
            'RDATE'
        )->expectBuilt('RDATE;VALUE=DATE:20200516');
    }

    /** @test */
    public function it_can_add_multiple_recurrence_dates()
    {
        $dateA = new DateTime('16 may 2019 12:00:00');
        $dateB = new DateTime('16 may 2020 15:00:00');

        $dateC = new DateTime('13 august 2019 12:00:00');
        $dateD = new DateTime('13 august 2020 15:00:00');

        $payload = Event::create()
            ->repeatOn([$dateA, $dateB])
            ->repeatOn([$dateC, $dateD], false)
            ->resolvePayload();

        PayloadExpectation::create($payload)
            ->expectProperty(
                'RDATE',
                function (PropertyExpectation $expectation) use ($dateA) {
                    $expectation
                        ->expectInstanceOf(DateTimeProperty::class)
                        ->expectValue($dateA)
                        ->expectParameterCount(1)
                        ->expectParameterValue('VALUE', 'DATE-TIME');
                },
                function (PropertyExpectation $expectation) use ($dateB, $dateA) {
                    $expectation
                        ->expectInstanceOf(DateTimeProperty::class)
                        ->expectValue($dateB)
                        ->expectParameterCount(1)
                        ->expectParameterValue('VALUE', 'DATE-TIME');
                },
                function (PropertyExpectation $expectation) use ($dateC, $dateA) {
                    $expectation
                        ->expectInstanceOf(DateTimeProperty::class)
                        ->expectValue($dateC)
                        ->expectParameterCount(1)
                        ->expectParameterValue('VALUE', 'DATE');
                },
                function (PropertyExpectation $expectation) use ($dateD, $dateA) {
                    $expectation
                        ->expectInstanceOf(DateTimeProperty::class)
                        ->expectValue($dateD)
                        ->expectParameterCount(1)
                        ->expectParameterValue('VALUE', 'DATE');
                }
            );
    }

    /** @test */
    public function it_can_add_excluded_recurrence_dates()
    {
        PropertyExpectation::create(
            Event::create()->doNotRepeatOn(new DateTime('16 may 2020 12:00:00'))->resolvePayload(),
            'EXDATE'
        )->expectBuilt('EXDATE;VALUE=DATE-TIME:20200516T120000Z');

        PropertyExpectation::create(
            Event::create()->doNotRepeatOn(new DateTime('16 may 2020 12:00:00'), false)->resolvePayload(),
            'EXDATE'
        )->expectBuilt('EXDATE;VALUE=DATE:20200516');
    }

    /** @test */
    public function it_can_add_multiple_excluded_recurrence_dates()
    {
        $dateA = new DateTime('16 may 2019 12:00:00');
        $dateB = new DateTime('16 may 2020 15:00:00');

        $dateC = new DateTime('13 august 2019 12:00:00');
        $dateD = new DateTime('13 august 2020 15:00:00');

        $payload = Event::create()
            ->doNotRepeatOn([$dateA, $dateB])
            ->doNotRepeatOn([$dateC, $dateD], false)
            ->resolvePayload();

        PayloadExpectation::create($payload)
            ->expectProperty(
                'EXDATE',
                function (PropertyExpectation $expectation) use ($dateA) {
                    $expectation
                        ->expectInstanceOf(DateTimeProperty::class)
                        ->expectValue($dateA)
                        ->expectParameterCount(1)
                        ->expectParameterValue('VALUE', 'DATE-TIME');
                },
                function (PropertyExpectation $expectation) use ($dateB, $dateA) {
                    $expectation
                        ->expectInstanceOf(DateTimeProperty::class)
                        ->expectValue($dateB)
                        ->expectParameterCount(1)
                        ->expectParameterValue('VALUE', 'DATE-TIME');
                },
                function (PropertyExpectation $expectation) use ($dateC, $dateA) {
                    $expectation
                        ->expectInstanceOf(DateTimeProperty::class)
                        ->expectValue($dateC)
                        ->expectParameterCount(1)
                        ->expectParameterValue('VALUE', 'DATE');
                },
                function (PropertyExpectation $expectation) use ($dateD, $dateA) {
                    $expectation
                        ->expectInstanceOf(DateTimeProperty::class)
                        ->expectValue($dateD)
                        ->expectParameterCount(1)
                        ->expectParameterValue('VALUE', 'DATE');
                }
            );
    }

    /** @test */
    public function it_can_add_an_attachment_to_an_event()
    {
        $payload = Event::create()
            ->attachment('http://spatie.be/logo.svg')
            ->attachment('http://spatie.be/logo.jpg', 'application/html')
            ->resolvePayload();

        PayloadExpectation::create($payload)->expectProperty(
            'ATTACH',
            fn(PropertyExpectation $expectation) => $expectation
                ->expectParameterCount(0)
                ->expectValue('http://spatie.be/logo.svg'),
            fn(PropertyExpectation $expectation) => $expectation
                ->expectParameterCount(1)
                ->expectParameterValue('FMTTYPE', 'application/html')
                ->expectValue('http://spatie.be/logo.jpg'),
        );
    }
}
