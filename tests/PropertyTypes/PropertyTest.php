<?php

namespace Spatie\Calendar\Tests\PropertyTypes;

use Exception;
use Spatie\Calendar\PropertyTypes\Property;
use Spatie\Calendar\PropertyTypes\TextProperty;
use Spatie\Calendar\Tests\TestCase;

class PropertyTest extends TestCase
{
    /** @test */
    public function a_property_can_give_a_specified_parameter()
    {
        $property = new TextProperty('NAME', 'Ruben');

        $property->addParameter(new TextProperty('LASTNAME', 'Van Assche'));

        $parameter = $property->getParameter('LASTNAME');

        $this->assertEquals('Van Assche', $parameter->getOriginalValue());
    }

    /** @test */
    public function an_exception_will_be_thrown_when_an_parameter_does_not_exist()
    {
        $this->expectException(Exception::class);

        $property = new TextProperty('NAME', 'Ruben');

        $property->addParameter(new TextProperty('LASTNAME', 'Van Assche'));

        $property->getParameter('LASTNAM');
    }
}
