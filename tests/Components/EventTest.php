<?php

namespace Spatie\Calendar\Tests\Components;

use Spatie\Calendar\Components\Event;
use Spatie\Calendar\PropertyTypes\TextProperty;
use Spatie\Calendar\Tests\TestCase;

class EventTest extends TestCase
{
    /** @test */
    public function it_can_create_an_event()
    {
        $payload = Event::new('An introduction into event sourcing')
            ->getPayload();

        $properties = $payload->getProperties();

        $this->assertEquals('EVENT', $payload->getType());
        $this->assertEquals(3, count($properties));

        $this->assertInArray(new TextProperty('SUMMARY', 'An introduction into event sourcing'), $properties);
    }
}
