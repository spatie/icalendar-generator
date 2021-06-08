<?php

namespace Spatie\IcalendarGenerator\Tests\Properties;

use Spatie\IcalendarGenerator\Properties\EmptyProperty;
use Spatie\IcalendarGenerator\Tests\TestCase;

class EmptyPropertyTest extends TestCase
{
    /** @test */
    public function it_can_create_an_empty_property_type()
    {
        $propertyType = new EmptyProperty('CONTACT', []);

        $this->assertEquals('CONTACT', $propertyType->getName());
        $this->assertEquals(null, $propertyType->getValue());
        $this->assertEquals(null, $propertyType->getOriginalValue());
    }
}
