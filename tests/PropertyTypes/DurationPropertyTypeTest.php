<?php

namespace Spatie\IcalendarGenerator\Tests\PropertyTypes;

use Spatie\IcalendarGenerator\Tests\TestCase;
use Spatie\IcalendarGenerator\PropertyTypes\DurationPropertyType;

final class DurationPropertyTypeTest extends TestCase
{
    /** @test */
    public function it_can_create_a_duration_property_type()
    {
        $property = new DurationPropertyType('DURATION', 5);

        $this->assertEquals(['DURATION'], $property->getNames());
        $this->assertEquals(5, $property->getOriginalValue());
        $this->assertEquals('PT5M', $property->getValue());
    }
}
