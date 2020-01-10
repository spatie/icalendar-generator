<?php

namespace Spatie\IcalendarGenerator\Tests\PropertyTypes;

use DateInterval;
use Spatie\IcalendarGenerator\PropertyTypes\DurationPropertyType;
use Spatie\IcalendarGenerator\Tests\TestCase;

final class DurationPropertyTypeTest extends TestCase
{
    /** @test */
    public function it_can_create_a_duration_property_type()
    {
        $interval = new DateInterval('PT5M');

        $property = new DurationPropertyType('DURATION', $interval);

        $this->assertEquals(['DURATION'], $property->getNames());
        $this->assertEquals($interval, $property->getOriginalValue());
        $this->assertEquals('PT5M', $property->getValue());
    }

    /** @test */
    public function it_can_invert_a_duration_property_type()
    {
        $interval = new DateInterval('PT5M');

        $property = DurationPropertyType::create('DURATION', $interval)->invert();

        $this->assertEquals(['DURATION'], $property->getNames());
        $this->assertEquals($interval, $property->getOriginalValue());
        $this->assertEquals('-PT5M', $property->getValue());
    }

    /** @test */
    public function it_can_create_a_duration_property_with_all_properties()
    {
        $interval = new DateInterval('P4DT3H2M1S');

        $property = new DurationPropertyType('DURATION', $interval);

        $this->assertEquals(['DURATION'], $property->getNames());
        $this->assertEquals($interval, $property->getOriginalValue());
        $this->assertEquals('P4DT3H2M1S', $property->getValue());
    }
}
