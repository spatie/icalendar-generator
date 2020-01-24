<?php

namespace Spatie\IcalendarGenerator\Tests\Components;

use DateTime;
use Spatie\IcalendarGenerator\Components\Alert;
use Spatie\IcalendarGenerator\Components\Event;
use Spatie\IcalendarGenerator\Enums\Classification;
use Spatie\IcalendarGenerator\Enums\EventStatus;
use Spatie\IcalendarGenerator\Enums\ParticipationStatus;
use Spatie\IcalendarGenerator\PropertyTypes\CalendarAddressPropertyType;
use Spatie\IcalendarGenerator\Tests\TestCase;
use Spatie\IcalendarGenerator\ValueObjects\CalendarAddress;

class EventTest extends TestCase
{
    /** @test */
    public function it_can_create_an_event()
    {
        $payload = Event::create()->resolvePayload();

        $properties = $payload->getProperties();

        $this->assertEquals('EVENT', $payload->getType());
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
            ->uniqueIdentifier('Identifier here')
            ->startsAt($dateStarts)
            ->endsAt($dateEnds)
            ->address('Antwerp')
            ->addressName('Spatie')
            ->resolvePayload();

        $this->assertCount(7, $payload->getProperties());

        $this->assertPropertyEqualsInPayload('SUMMARY', 'An introduction into event sourcing', $payload);
        $this->assertPropertyEqualsInPayload('DESCRIPTION', 'By Freek Murze', $payload);
        $this->assertPropertyEqualsInPayload('DTSTAMP', $dateCreated, $payload);
        $this->assertPropertyEqualsInPayload('DTSTART', $dateStarts, $payload);
        $this->assertPropertyEqualsInPayload('DTEND', $dateEnds, $payload);
        $this->assertPropertyEqualsInPayload('LOCATION', 'Antwerp', $payload);
        $this->assertPropertyEqualsInPayload('UID', 'Identifier here', $payload);
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

        $payload->getProperty('DTSTART');

        $this->assertPropertyEqualsInPayload('DTSTART', $dateStarts, $payload);
        $this->assertPropertyEqualsInPayload('DTEND', $dateEnds, $payload);
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

        $this->assertEquals('51.2343;4.4287', $property->getValue());
        $this->assertEquals([
            'lat' => 51.2343,
            'lng' => 4.4287,
        ], $property->getOriginalValue());
        $this->assertParameterEqualsInProperty('VALUE', 'URI', $property);
        $this->assertParameterEqualsInProperty('X-ADDRESS', 'Samberstraat 69D, 2060 Antwerpen, Belgium', $property);
        $this->assertParameterEqualsInProperty('X-APPLE-RADIUS', 72, $property);
        $this->assertParameterEqualsInProperty('X-TITLE', 'Spatie HQ', $property);
    }

    /** @test */
    public function it_can_add_a_classification()
    {
        $payload = Event::create()
            ->classification(Classification::private())
            ->resolvePayload();

        $this->assertPropertyEqualsInPayload('CLASS', Classification::private()->getValue(), $payload);
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

        $this->assertContainsEquals(CalendarAddressPropertyType::create(
            'ATTENDEE',
            new CalendarAddress('ruben@spatie.be')
        ), $payload);

        $this->assertContainsEquals(CalendarAddressPropertyType::create(
            'ATTENDEE',
            new CalendarAddress('brent@spatie.be', 'Brent')
        ), $payload);

        $this->assertContainsEquals(CalendarAddressPropertyType::create(
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
            EventStatus::tentative()->getValue(),
            $payload
        );
    }
}
