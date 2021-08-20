<?php

namespace Spatie\IcalendarGenerator\Tests\Properties;

use DateInterval;
use Spatie\IcalendarGenerator\Properties\DurationProperty;
use Spatie\IcalendarGenerator\Tests\PropertyExpectation;
use Spatie\IcalendarGenerator\Tests\TestCase;

class DurationPropertyTest extends TestCase
{
    /** @test */
    public function it_can_create_a_duration_property_type()
    {
        $interval = new DateInterval('PT5M');

        $property = new DurationProperty('DURATION', $interval);

        PropertyExpectation::create($property)
            ->expectName('DURATION')
            ->expectValue($interval)
            ->expectOutput('PT5M');
    }
}
