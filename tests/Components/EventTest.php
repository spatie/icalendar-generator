<?php

namespace Spatie\Calendar\Tests\Components;

use DateTime;
use Spatie\Calendar\Components\Event;
use Spatie\Calendar\Tests\TestCase;

class EventTest extends TestCase
{
    /** @test */
    public function it_can_create_an_event()
    {
        $payload = Event::new()->getPayload();

        $properties = $payload->getProperties();

        $this->assertEquals('EVENT', $payload->getType());
        $this->assertEquals(2, count($properties));

        $this->assertPropertyExistInPayload('UID', $payload);
        $this->assertPropertyExistInPayload('DTSTAMP', $payload);
    }

    /** @test */
    public function it_can_set_properties_on_an_event()
    {
        $dateCreated = new DateTime('16 may 2019');
        $dateStarts = new DateTime('17 may 2019');
        $dateEnds = new DateTime('18 may 2019');

        $payload = Event::new('An introduction into event sourcing')
            ->description('By Freek Murze')
            ->created($dateCreated)
            ->uniqueIdentifier('Identifier here')
            ->starts($dateStarts)
            ->ends($dateEnds)
            ->location('Antwerp')
            ->getPayload();

        $this->assertEquals(7, count($payload->getProperties()));

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

        $payload = Event::new('An introduction into event sourcing')
            ->period($dateStarts, $dateEnds)
            ->getPayload();

        $this->assertPropertyEqualsInPayload('DTSTART', $dateStarts, $payload);
        $this->assertPropertyEqualsInPayload('DTEND', $dateEnds, $payload);
    }

    // TODO: test this
    public function an_event_can_be_a_full_day()
    {
        $dateStarts = new DateTime('17 may 2019');
        $dateEnds = new DateTime('18 may 2019');

        $payload = Event::new('An introduction into event sourcing')
            ->fullDay()
            ->period($dateStarts, $dateEnds)
            ->getPayload();

        $payload->getProperty('DTSTART');

        $this->assertPropertyEqualsInPayload('DTSTART', $dateStarts, $payload);
        $this->assertPropertyEqualsInPayload('DTEND', $dateEnds, $payload);
    }

}
