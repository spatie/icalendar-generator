<?php

namespace Spatie\IcalendarGenerator\Tests\ValueObjects;

use DateInterval;
use Spatie\IcalendarGenerator\Tests\TestCase;
use Spatie\IcalendarGenerator\ValueObjects\DurationValue;

class DurationValueTest extends TestCase
{
    /** @test */
    public function it_can_create_a_duration_property_type()
    {
        $value = DurationValue::create(new DateInterval('PT5M'));

        $this->assertEquals('PT5M', $value->format());
    }

    /** @test */
    public function it_can_invert_a_duration_property_type()
    {
        $value = DurationValue::create(new DateInterval('PT5M'))->invert();

        $this->assertEquals('-PT5M', $value->format());
    }

    /** @test */
    public function it_can_create_a_duration_property_with_all_properties()
    {
        $value = DurationValue::create(new DateInterval('P4DT3H2M1S'));

        $this->assertEquals('P4DT3H2M1S', $value->format());
    }

    /** @test */
    public function it_can_use_a_regular_string_as_duration()
    {
        $value = DurationValue::create('PT5M');

        $this->assertEquals('PT5M', $value->format());
    }
}
