<?php

namespace Spatie\IcalendarGenerator\Tests\Components;

use DateInterval;
use DateTime;
use Spatie\IcalendarGenerator\Components\Alert;
use Spatie\IcalendarGenerator\Tests\PayloadExpectation;
use Spatie\IcalendarGenerator\Tests\PropertyExpectation;
use Spatie\IcalendarGenerator\Tests\TestCase;

class AlertTest extends TestCase
{
    /** @test */
    public function it_can_create_an_alert_at_a_date()
    {
        $trigger = new DateTime('16 may 2019');

        $payload = (new Alert('It is time'))->triggerDate($trigger)->resolvePayload();

        PayloadExpectation::create($payload)
            ->expectType('VALARM')
            ->expectPropertyCount(3)
            ->expectPropertyValue('ACTION', 'DISPLAY')
            ->expectPropertyValue('DESCRIPTION', 'It is time')
            ->expectProperty('TRIGGER', function (PropertyExpectation  $expectation) use ($trigger) {
                $expectation
                    ->expectValue($trigger)
                    ->expectParameterCount(1)
                    ->expectParameterValue('VALUE', 'DATE-TIME');
            });
    }

    /** @test */
    public function it_can_create_an_alert_without_timezone_at_a_date()
    {
        $trigger = new DateTime('16 may 2019');

        $payload = (new Alert('It is time'))
            ->withoutTimezone()
            ->triggerDate($trigger)
            ->resolvePayload();

        PropertyExpectation::create($payload, 'TRIGGER')
            ->expectParameterCount(1)
            ->expectParameterValue('VALUE', 'DATE-TIME');
    }

    /** @test */
    public function it_can_create_an_alert_at_the_start_of_an_event()
    {
        $trigger = new DateInterval('PT5M');

        $payload = (new Alert())
            ->triggerAtStart($trigger)
            ->resolvePayload();

        PayloadExpectation::create($payload)
            ->expectType('VALARM')
            ->expectPropertyCount(2)
            ->expectPropertyValue('ACTION', 'DISPLAY')
            ->expectProperty('TRIGGER', function (PropertyExpectation $expectation) use ($trigger) {
                $expectation->expectValue($trigger)->expectParameterCount(0);
            });
    }

    /** @test */
    public function it_can_create_an_alert_at_the_end_of_an_event()
    {
        $trigger = new DateInterval('PT5M');

        $payload = (new Alert())
            ->triggerAtEnd($trigger)
            ->resolvePayload();

        PayloadExpectation::create($payload)
            ->expectType('VALARM')
            ->expectPropertyCount(2)
            ->expectPropertyValue('ACTION', 'DISPLAY')
            ->expectProperty('TRIGGER', function (PropertyExpectation $expectation) use ($trigger) {
                $expectation->expectValue($trigger)
                    ->expectParameterCount(1)
                    ->expectParameterValue('RELATED', 'END');
            });
    }

    /** @test */
    public function it_can_be_constructed_as_static_before_or_after()
    {
        $interval = new DateInterval('PT5M');

        $payload = Alert::minutesBeforeStart(5)->resolvePayload();
        $interval->invert = 1;

        PropertyExpectation::create($payload, 'TRIGGER')
            ->expectValue($interval)
            ->expectParameterCount(0);

        $payload = Alert::minutesAfterStart(5)->resolvePayload();
        $interval->invert = 0;

        PropertyExpectation::create($payload, 'TRIGGER')
            ->expectValue($interval)
            ->expectParameterCount(0);

        $payload = Alert::minutesBeforeEnd(5)->resolvePayload();
        $interval->invert = 1;

        PropertyExpectation::create($payload, 'TRIGGER')
            ->expectValue($interval)
            ->expectParameterCount(1)
            ->expectParameterValue('RELATED', 'END');

        $payload = Alert::minutesAfterEnd(5)->resolvePayload();
        $interval->invert = 0;

        PropertyExpectation::create($payload, 'TRIGGER')
            ->expectValue($interval)
            ->expectParameterCount(1)
            ->expectParameterValue('RELATED', 'END');
    }
}
