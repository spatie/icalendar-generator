<?php

namespace Spatie\Calendar\Tests\PropertyTypes;

use Spatie\Calendar\PropertyTypes\DurationPropertyType;
use Spatie\Calendar\Tests\TestCase;

final class DurationPropertyTypeTest extends TestCase
{
    /** @test */
    public function it_can_create_a_duration_property_type()
    {
        $property = new DurationPropertyType('DURATION', 5);

        $this->assertEquals('DURATION', $property->getName());
        $this->assertEquals(5, $property->getOriginalValue());
        $this->assertEquals('PT5M', $property->getValue());
    }
}
