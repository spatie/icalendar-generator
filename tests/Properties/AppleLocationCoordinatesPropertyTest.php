<?php

namespace Spatie\IcalendarGenerator\Tests\Properties;

use Spatie\IcalendarGenerator\Properties\AppleLocationCoordinatesProperty;
use Spatie\IcalendarGenerator\Tests\TestCase;

class AppleLocationCoordinatesPropertyTest extends TestCase
{
    /** @test */
    public function it_can_create_an_apple_location_coordinates_property_type()
    {
        $propertyType = new AppleLocationCoordinatesProperty(10.5, 20.5, 'Samberstraat 69D, 2060 Antwerpen, Belgium', 'Spatie HQ', 72);

        $this->assertEquals('X-APPLE-STRUCTURED-LOCATION', $propertyType->getName());
        $this->assertEquals('geo:10.5,20.5', $propertyType->getValue());
        $this->assertEquals('URI', $propertyType->getParameter('VALUE')->getValue());
        $this->assertEquals('Samberstraat 69D\, 2060 Antwerpen\, Belgium', $propertyType->getParameter('X-ADDRESS')->getValue());
        $this->assertEquals('Spatie HQ', $propertyType->getParameter('X-TITLE')->getValue());
        $this->assertEquals(72, $propertyType->getParameter('X-APPLE-RADIUS')->getValue());
        $this->assertEquals([
            'lat' => 10.5,
            'lng' => 20.5,
        ], $propertyType->getOriginalValue());
    }
}
