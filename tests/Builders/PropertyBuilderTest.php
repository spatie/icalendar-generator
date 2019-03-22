<?php

namespace Spatie\Calendar\Tests\Builders;

use Spatie\Calendar\Tests\TestCase;
use Spatie\Calendar\Builders\PropertyBuilder;
use Spatie\Calendar\PropertyTypes\Parameter;
use Spatie\Calendar\PropertyTypes\TextPropertyType;
use Spatie\Calendar\Tests\Dummy\DummyPropertyType;

class PropertyBuilderTest extends TestCase
{
    /** @test */
    public function it_will_build_the_property_correctly()
    {
        $property = new DummyPropertyType('location', 'Antwerp');

        $this->assertEquals(
            'location:Antwerp',
            (new PropertyBuilder($property))->build()
        );
    }

    /** @test */
    public function it_will_build_the_parameters_correctly()
    {
        $property = new DummyPropertyType('location', 'Antwerp');

        $property->addParameter(
            new Parameter('street', 'Samberstraat')
        );

        $this->assertEquals(
            'location;street=Samberstraat:Antwerp',
            (new PropertyBuilder($property))->build()
        );
    }

    /** @test */
    public function it_will_build_the_property_according_to_specific_rules()
    {
        $property = new TextPropertyType('location', 'Antwerp, Belgium');

        $this->assertEquals(
            'location:Antwerp\, Belgium',
            (new PropertyBuilder($property))->build()
        );
    }
}
