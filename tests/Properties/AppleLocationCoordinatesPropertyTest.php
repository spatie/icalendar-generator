<?php

namespace Spatie\IcalendarGenerator\Tests\Properties;

use Spatie\IcalendarGenerator\Properties\AppleLocationCoordinatesProperty;
use Spatie\IcalendarGenerator\Tests\PropertyExpectation;
use Spatie\IcalendarGenerator\Tests\TestCase;

class AppleLocationCoordinatesPropertyTest extends TestCase
{
    /** @test */
    public function it_can_create_an_apple_location_coordinates_property_type()
    {
        $propertyType = new AppleLocationCoordinatesProperty(10.5, 20.5, 'Samberstraat 69D, 2060 Antwerpen, Belgium', 'Spatie HQ', 72);

        PropertyExpectation::create($propertyType)
            ->expectName('X-APPLE-STRUCTURED-LOCATION')
            ->expectValue(['lat' => 10.5, 'lng' => 20.5,])
            ->expectOutput('geo:10.5,20.5')
            ->expectParameterValue('VALUE', 'URI')
            ->expectParameterValue('X-ADDRESS', 'Samberstraat 69D\, 2060 Antwerpen\, Belgium')
            ->expectParameterValue('X-TITLE', 'Spatie HQ')
            ->expectParameterValue('X-APPLE-RADIUS', 72);
    }
}
