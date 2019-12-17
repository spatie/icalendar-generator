<?php

namespace Spatie\IcalendarGenerator\Tests\Components;

use DateTime;
use Spatie\IcalendarGenerator\Components\Alert;
use Spatie\IcalendarGenerator\Tests\TestCase;

class AlertTest extends TestCase
{
    /** @test */
    public function it_can_create_an_alert()
    {
        $trigger = new DateTime('16 may 2019');

        $payload = Alert::create($trigger, 'It is time')->resolvePayload();

        $this->assertEquals('ALARM', $payload->getType());
        $this->assertCount(3, $payload->getProperties());

        $this->assertPropertyEqualsInPayload('ACTION', 'DISPLAY', $payload);
        $this->assertPropertyEqualsInPayload('DESCRIPTION', 'It is time', $payload);
        $this->assertPropertyEqualsInPayload('TRIGGER', $trigger, $payload);
        $this->assertParameterEqualsInProperty('VALUE', 'DATE-TIME', $payload->getProperty('TRIGGER'));
    }
}
