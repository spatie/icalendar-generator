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
use Spatie\IcalendarGenerator\Properties\CalendarAddressProperty;
use Spatie\IcalendarGenerator\Properties\DateTimeProperty;
use Spatie\IcalendarGenerator\Properties\Parameter;
use Spatie\IcalendarGenerator\Tests\TestCase;
use Spatie\IcalendarGenerator\ValueObjects\CalendarAddress;
use Spatie\IcalendarGenerator\ValueObjects\DateTimeValue;
use Spatie\IcalendarGenerator\ValueObjects\RRule;

class EventTest extends TestCase
{
    /** @test */
    public function it_can_create_an_event()
    {
        $payload = Event::create()->resolvePayload();

        $properties = $payload->getProperties();

        $this->assertEquals('VEVENT', $payload->getType());
        $this->assertCount(2, $properties);

        $this->assertPropertyExistInPayload('UID', $payload);
        $this->assertPropertyExistInPayload('DTSTAMP', $payload);
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

        $this->assertCount(8, $payload->getProperties());

        $this->assertPropertyEqualsInPayload('SUMMARY', 'An introduction into event sourcing', $payload);
        $this->assertPropertyEqualsInPayload('DESCRIPTION', 'By Freek Murze', $payload);
        $this->assertPropertyEqualsInPayload('DTSTAMP', $dateCreated, $payload);
        $this->assertPropertyEqualsInPayload('DTSTART', $dateStarts, $payload);
        $this->assertPropertyEqualsInPayload('DTEND', $dateEnds, $payload);
        $this->assertPropertyEqualsInPayload('LOCATION', 'Antwerp', $payload);
        $this->assertPropertyEqualsInPayload('UID', 'Identifier here', $payload);
        $this->assertPropertyEqualsInPayload('URL', 'http://example.com/pub/calendars/jsmith/mytime.ics', $payload);
    }

    /** @test */
    public function it_can_set_a_period_on_an_event()
    {
        $dateStarts = new DateTime('17 may 2019');
        $dateEnds = new DateTime('18 may 2019');

        $payload = Event::create('An introduction into event sourcing')
            ->period($dateStarts, $dateEnds)
            ->resolvePayload();

        $this->assertPropertyEqualsInPayload('DTSTART', $dateStarts, $payload);
        $this->assertPropertyEqualsInPayload('DTEND', $dateEnds, $payload);
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

        $this->assertPropertyEqualsInPayload('DTSTART', null, $payload);
        $this->assertParameterEqualsInProperty('VALUE', 'DATE:20190517', $payload->getProperty('DTSTART'));

        $this->assertPropertyEqualsInPayload('DTEND', null, $payload);
        $this->assertParameterEqualsInProperty('VALUE', 'DATE:20190518', $payload->getProperty('DTEND'));
    }

    /** @test */
    public function an_event_can_be_a_full_day_without_specifying_an_end()
    {
        $dateStarts = new DateTime('17 may 2019');

        $payload = Event::create('An introduction into event sourcing')
            ->fullDay()
            ->startsAt($dateStarts)
            ->resolvePayload();

        $this->assertPropertyEqualsInPayload('DTSTART', null, $payload);
        $this->assertParameterEqualsInProperty('VALUE', 'DATE:20190517', $payload->getProperty('DTSTART'));

        $this->assertPropertyNotInPayload('DTEND', $payload);
    }

    /** @test */
    public function it_can_alert_minutes_before_an_event()
    {
        $payload = Event::create()
            ->alertMinutesBefore(5)
            ->resolvePayload();

        $subcomponents = $payload->getSubComponents();

        $this->assertCount(1, $subcomponents);
        $this->assertInstanceOf(Alert::class, $subcomponents[0]);
    }

    /** @test */
    public function it_can_alert_minutes_after_an_event()
    {
        $payload = Event::create()
            ->alertMinutesAfter(5)
            ->resolvePayload();

        $subcomponents = $payload->getSubComponents();

        $this->assertCount(1, $subcomponents);
        $this->assertInstanceOf(Alert::class, $subcomponents[0]);
    }

    /** @test */
    public function it_can_add_an_alert()
    {
        $payload = Event::create()
            ->alert(new Alert('Test'))
            ->resolvePayload();

        $subcomponents = $payload->getSubComponents();

        $this->assertCount(1, $subcomponents);
        $this->assertInstanceOf(Alert::class, $subcomponents[0]);
    }

    /** @test */
    public function it_can_set_the_coordinates()
    {
        $payload = Event::create('An introduction into event sourcing')
            ->coordinates(51.2343, 4.4287)
            ->resolvePayload();

        $this->assertPropertyEqualsInPayload('GEO', [
            'lat' => 51.2343,
            'lng' => 4.4287,
        ], $payload);
    }

    /** @test */
    public function it_can_generate_an_apple_structured_location()
    {
        $payload = Event::create('An introduction into event sourcing')
            ->coordinates(51.2343, 4.4287)
            ->address('Samberstraat 69D, 2060 Antwerpen, Belgium')
            ->addressName('Spatie HQ')
            ->resolvePayload();

        $this->assertPropertyExistInPayload('X-APPLE-STRUCTURED-LOCATION', $payload);

        $property = $payload->getProperty('X-APPLE-STRUCTURED-LOCATION');

        $this->assertEquals('geo:51.2343,4.4287', $property->getValue());
        $this->assertEquals([
            'lat' => 51.2343,
            'lng' => 4.4287,
        ], $property->getOriginalValue());
        $this->assertParameterEqualsInProperty('VALUE', 'URI', $property);
        $this->assertParameterEqualsInProperty('X-ADDRESS', 'Samberstraat 69D\, 2060 Antwerpen\, Belgium', $property);
        $this->assertParameterEqualsInProperty('X-APPLE-RADIUS', 72, $property);
        $this->assertParameterEqualsInProperty('X-TITLE', 'Spatie HQ', $property);
    }

    /** @test */
    public function it_can_add_a_classification()
    {
        $payload = Event::create()
            ->classification(Classification::private())
            ->resolvePayload();

        $this->assertPropertyEqualsInPayload('CLASS', Classification::private()->value, $payload);
    }

    /** @test */
    public function it_can_make_an_event_transparent()
    {
        $payload = Event::create()
            ->transparent()
            ->resolvePayload();

        $this->assertPropertyEqualsInPayload('TRANSP', 'TRANSPARENT', $payload);
    }

    /** @test */
    public function it_can_add_an_organizer()
    {
        $payload = Event::create()
            ->organizer('ruben@spatie.be', 'Ruben')
            ->resolvePayload();

        $this->assertPropertyEqualsInPayload(
            'ORGANIZER',
            new CalendarAddress('ruben@spatie.be', 'Ruben'),
            $payload
        );
    }

    /** @test */
    public function it_can_add_attendees()
    {
        $payload = Event::create()
            ->attendee('ruben@spatie.be')
            ->attendee('brent@spatie.be', 'Brent')
            ->attendee('adriaan@spatie.be', 'Adriaan', ParticipationStatus::declined())
            ->resolvePayload()
            ->getProperties();

        $this->assertContainsEquals(CalendarAddressProperty::create(
            'ATTENDEE',
            new CalendarAddress('ruben@spatie.be')
        ), $payload);

        $this->assertContainsEquals(CalendarAddressProperty::create(
            'ATTENDEE',
            new CalendarAddress('brent@spatie.be', 'Brent')
        ), $payload);

        $this->assertContainsEquals(CalendarAddressProperty::create(
            'ATTENDEE',
            new CalendarAddress('adriaan@spatie.be', 'Adriaan', ParticipationStatus::declined())
        ), $payload);
    }

    /** @test */
    public function it_can_set_a_status()
    {
        $payload = Event::create()
            ->status(EventStatus::tentative())
            ->resolvePayload();

        $this->assertPropertyEqualsInPayload(
            'STATUS',
            EventStatus::tentative()->value,
            $payload
        );
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

        $this->assertPropertyEqualsInPayload('LOCATION', 'Antwerp', $payload);
    }

    /** @test */
    public function it_can_set_an_recurrence_rule()
    {
        $payload = Event::create('An introduction into event sourcing')
            ->rrule($rrule = RRule::frequency(RecurrenceFrequency::daily()))
            ->resolvePayload();

        $this->assertPropertyEqualsInPayload('RRULE', $rrule, $payload);
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

        $this->assertParameterCountInProperty(0, $payload->getProperty('DTSTART'));
        $this->assertParameterCountInProperty(0, $payload->getProperty('DTEND'));
        $this->assertParameterCountInProperty(0, $payload->getProperty('DTSTAMP'));

        /** @var \Spatie\IcalendarGenerator\ComponentPayload $alert */
        $alert = $payload->getSubComponents()[0]->resolvePayload();

        $this->assertParameterCountInProperty(1, $alert->getProperty('TRIGGER'));
        $this->assertParameterEqualsInProperty('VALUE', 'DATE-TIME', $alert->getProperty('TRIGGER'));
    }

    /** @test */
    public function it_can_set_a_url()
    {
        $payload = Event::create()
            ->url('http://example.com/pub/calendars/jsmith/mytime.ics')
            ->resolvePayload();

        $this->assertPropertyEqualsInPayload('URL', 'http://example.com/pub/calendars/jsmith/mytime.ics', $payload);
    }

    /** @test */
    public function it_ignores_a_wrong_url()
    {
        $payload = Event::create()
            ->url('xample.com/pub/calendars/jsmith/mytime.ics')
            ->resolvePayload();

        $this->assertPropertyNotInPayload('URL', $payload);
    }

    /** @test */
    public function it_will_always_use_utc_for_a_created_date_stamp()
    {
        $created = new DateTime('16 may 2020 12:00:00', new DateTimeZone('Europe/Brussels'));

        $payload = Event::create()
            ->createdAt($created)
            ->resolvePayload();

        $this->assertPropertyEqualsInPayload(
            'DTSTAMP',
            new DateTime('16 may 2020 10:00:00', new DateTimeZone('UTC')),
            $payload
        );
    }

    /** @test */
    public function it_can_add_recurrence_dates()
    {
        $this->assertBuildPropertyEqualsInPayload(
            'RDATE',
            'RDATE;VALUE=DATE-TIME:20200516T120000Z',
            Event::create()->repeatOn(new DateTime('16 may 2020 12:00:00'))->resolvePayload()
        );

        $this->assertBuildPropertyEqualsInPayload(
            'RDATE',
            'RDATE;VALUE=DATE:20200516',
            Event::create()->repeatOn(new DateTime('16 may 2020 12:00:00'), false)->resolvePayload()
        );
    }

    /** @test */
    public function it_can_add_multiple_recurrence_dates()
    {
        $dateA = new DateTime('16 may 2019 12:00:00');
        $dateB = new DateTime('16 may 2020 15:00:00');

        $dateC = new DateTime('13 august 2019 12:00:00');
        $dateD = new DateTime('13 august 2020 15:00:00');

        $properties = Event::create()
            ->repeatOn([$dateA, $dateB])
            ->repeatOn([$dateC, $dateD], false)
            ->resolvePayload()
            ->getProperties();

        $this->assertContainsEquals(
            DateTimeProperty::create('RDATE', DateTimeValue::create($dateA, true))
                ->addParameter(Parameter::create('VALUE', 'DATE-TIME')),
            $properties
        );

        $this->assertContainsEquals(
            DateTimeProperty::create('RDATE', DateTimeValue::create($dateB, true))
                ->addParameter(Parameter::create('VALUE', 'DATE-TIME')),
            $properties
        );

        $this->assertContainsEquals(
            DateTimeProperty::create('RDATE', DateTimeValue::create($dateC, false))
                ->addParameter(Parameter::create('VALUE', 'DATE')),
            $properties
        );

        $this->assertContainsEquals(
            DateTimeProperty::create('RDATE', DateTimeValue::create($dateD, false))
                ->addParameter(Parameter::create('VALUE', 'DATE')),
            $properties
        );
    }

    /** @test */
    public function it_can_add_excluded_recurrence_dates()
    {
        $this->assertBuildPropertyEqualsInPayload(
            'EXDATE',
            'EXDATE;VALUE=DATE-TIME:20200516T120000Z',
            Event::create()->doNotRepeatOn(new DateTime('16 may 2020 12:00:00'))->resolvePayload()
        );

        $this->assertBuildPropertyEqualsInPayload(
            'EXDATE',
            'EXDATE;VALUE=DATE:20200516',
            Event::create()->doNotRepeatOn(new DateTime('16 may 2020 12:00:00'), false)->resolvePayload()
        );
    }

    /** @test */
    public function it_can_add_multiple_excluded_recurrence_dates()
    {
        $dateA = new DateTime('16 may 2019 12:00:00');
        $dateB = new DateTime('16 may 2020 15:00:00');

        $dateC = new DateTime('13 august 2019 12:00:00');
        $dateD = new DateTime('13 august 2020 15:00:00');

        $properties = Event::create()
            ->doNotRepeatOn([$dateA, $dateB])
            ->doNotRepeatOn([$dateC, $dateD], false)
            ->resolvePayload()
            ->getProperties();

        $this->assertContainsEquals(
            DateTimeProperty::create('EXDATE', DateTimeValue::create($dateA, true))
                ->addParameter(Parameter::create('VALUE', 'DATE-TIME')),
            $properties
        );

        $this->assertContainsEquals(
            DateTimeProperty::create('EXDATE', DateTimeValue::create($dateB, true))
                ->addParameter(Parameter::create('VALUE', 'DATE-TIME')),
            $properties
        );

        $this->assertContainsEquals(
            DateTimeProperty::create('EXDATE', DateTimeValue::create($dateC, false))
                ->addParameter(Parameter::create('VALUE', 'DATE')),
            $properties
        );

        $this->assertContainsEquals(
            DateTimeProperty::create('EXDATE', DateTimeValue::create($dateD, false))
                ->addParameter(Parameter::create('VALUE', 'DATE')),
            $properties
        );
    }
}
