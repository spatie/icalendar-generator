<?php

namespace Spatie\IcalendarGenerator\Tests\Properties;

use DateInterval;
use Spatie\IcalendarGenerator\Properties\DurationProperty;
use Spatie\IcalendarGenerator\Tests\TestCase;

class DurationPropertyTest extends TestCase
{
    /** @test */
    public function it_can_create_a_duration_property_type()
    {
        $interval = new DateInterval('PT5M');

        $property = new DurationProperty('DURATION', $interval);

        $this->assertEquals('DURATION', $property->getName());
        $this->assertEquals($interval, $property->getOriginalValue());
        $this->assertEquals('PT5M', $property->getValue());
    }

    /** @test */
    public function it_can_invert_a_duration_property_type()
    {
        $interval = new DateInterval('PT5M');

        $property = DurationProperty::create('DURATION', $interval)->invert();

        $this->assertEquals('DURATION', $property->getName());
        $this->assertEquals($interval, $property->getOriginalValue());
        $this->assertEquals('-PT5M', $property->getValue());
    }

    /** @test */
    public function it_can_create_a_duration_property_with_all_properties()
    {
        $interval = new DateInterval('P4DT3H2M1S');

        $property = new DurationProperty('DURATION', $interval);

        $this->assertEquals('DURATION', $property->getName());
        $this->assertEquals($interval, $property->getOriginalValue());
        $this->assertEquals('P4DT3H2M1S', $property->getValue());
    }
}
