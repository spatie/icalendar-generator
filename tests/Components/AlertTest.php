<?php

namespace Spatie\IcalendarGenerator\Tests\Components;

use DateInterval;
use DateTime;
use Spatie\IcalendarGenerator\Components\Alert;
use Spatie\IcalendarGenerator\Tests\TestCase;

class AlertTest extends TestCase
{
    /** @test */
    public function it_can_create_an_alert_at_a_date()
    {
        $trigger = new DateTime('16 may 2019');

        $payload = (new Alert('It is time'))->triggerDate($trigger)->resolvePayload();

        $this->assertEquals('ALARM', $payload->getType());
        $this->assertCount(3, $payload->getProperties());

        $this->assertPropertyEqualsInPayload('ACTION', 'DISPLAY', $payload);
        $this->assertPropertyEqualsInPayload('DESCRIPTION', 'It is time', $payload);
        $this->assertPropertyEqualsInPayload('TRIGGER', $trigger, $payload);
        $this->assertParameterEqualsInProperty('VALUE', 'DATE-TIME', $payload->getProperty('TRIGGER'));
        $this->assertParameterCountInProperty(1, $payload->getProperty('TRIGGER'));
    }

    /** @test */
    public function it_can_create_an_alert_at_the_start_of_an_event()
    {
        $trigger = new DateInterval('PT5M');

        $payload = (new Alert())
            ->triggerAtStart($trigger)
            ->resolvePayload();

        $this->assertEquals('ALARM', $payload->getType());
        $this->assertCount(2, $payload->getProperties());

        $this->assertPropertyEqualsInPayload('ACTION', 'DISPLAY', $payload);
        $this->assertPropertyEqualsInPayload('TRIGGER', $trigger, $payload);
        $this->assertParameterCountInProperty(0, $payload->getProperty('TRIGGER'));
    }

    /** @test */
    public function it_can_create_an_alert_at_the_end_of_an_event()
    {
        $trigger = new DateInterval('PT5M');

        $payload = (new Alert())
            ->triggerAtEnd($trigger)
            ->resolvePayload();

        $this->assertEquals('ALARM', $payload->getType());
        $this->assertCount(2, $payload->getProperties());

        $this->assertPropertyEqualsInPayload('ACTION', 'DISPLAY', $payload);
        $this->assertPropertyEqualsInPayload('TRIGGER', $trigger, $payload);
        $this->assertParameterCountInProperty(1, $payload->getProperty('TRIGGER'));
        $this->assertParameterEqualsInProperty('RELATED', 'END', $payload->getProperty('TRIGGER'));
    }

    /** @test */
    public function it_can_be_constructed_as_static_before_or_after()
    {
        $interval = new DateInterval('PT5M');

        $payload = Alert::minutesBeforeStart(5)->resolvePayload();
        $interval->invert = 1;

        $this->assertPropertyEqualsInPayload('TRIGGER', $interval, $payload);
        $this->assertParameterCountInProperty(0, $payload->getProperty('TRIGGER'));

        $payload = Alert::minutesAfterStart(5)->resolvePayload();
        $interval->invert = 0;

        $this->assertPropertyEqualsInPayload('TRIGGER', $interval, $payload);
        $this->assertParameterCountInProperty(0, $payload->getProperty('TRIGGER'));

        $payload = Alert::minutesBeforeEnd(5)->resolvePayload();
        $interval->invert = 1;

        $this->assertPropertyEqualsInPayload('TRIGGER', $interval, $payload);
        $this->assertParameterCountInProperty(1, $payload->getProperty('TRIGGER'));
        $this->assertParameterEqualsInProperty('RELATED', 'END', $payload->getProperty('TRIGGER'));

        $payload = Alert::minutesAfterEnd(5)->resolvePayload();
        $interval->invert = 0;

        $this->assertPropertyEqualsInPayload('TRIGGER', $interval, $payload);
        $this->assertParameterCountInProperty(1, $payload->getProperty('TRIGGER'));
        $this->assertParameterEqualsInProperty('RELATED', 'END', $payload->getProperty('TRIGGER'));
    }
}
