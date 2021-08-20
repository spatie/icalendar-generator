<?php

namespace Spatie\IcalendarGenerator\Tests\Properties;

use Spatie\IcalendarGenerator\Properties\CoordinatesProperty;
use Spatie\IcalendarGenerator\Tests\PropertyExpectation;
use Spatie\IcalendarGenerator\Tests\TestCase;

class CoordinatesPropertyTest extends TestCase
{
    /** @test */
    public function it_can_create_a_coordinates_property_type()
    {
        $propertyType = new CoordinatesProperty('GEO', 10.5, 20.5);

        PropertyExpectation::create($propertyType)
            ->expectName('GEO')
            ->expectOutput('10.5;20.5')
            ->expectValue(['lat' => 10.5, 'lng' => 20.5]);
    }
}
