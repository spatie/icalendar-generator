<?php

namespace Spatie\Calendar\Tests\PropertyTypes;

use Spatie\Calendar\PropertyTypes\BooleanPropertyType;
use Spatie\Calendar\Tests\TestCase;

class BooleanPropertyTypeTest extends TestCase
{
    /** @test */
    public function it_will_use_the_correct_values()
    {
        $property = new BooleanPropertyType('IS_SET', true);

        $this->assertEquals('TRUE', $property->getValue());

        $property = new BooleanPropertyType('IS_SET', false);

        $this->assertEquals('FALSE', $property->getValue());
    }
}
