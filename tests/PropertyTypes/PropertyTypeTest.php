<?php

namespace Spatie\IcalendarGenerator\Tests\PropertyTypes;

use Exception;
use Spatie\IcalendarGenerator\PropertyTypes\Parameter;
use Spatie\IcalendarGenerator\PropertyTypes\TextPropertyType;
use Spatie\IcalendarGenerator\Tests\TestCase;

class PropertyTypeTest extends TestCase
{
    /** @test */
    public function a_property_can_give_a_specified_parameter()
    {
        $property = new TextPropertyType('NAME', 'Ruben');

        $property->addParameter(new Parameter('LASTNAME', 'Van Assche'));

        $parameter = $property->getParameter('LASTNAME');

        $this->assertEquals('Van Assche', $parameter->getValue());
    }

    /** @test */
    public function an_exception_will_be_thrown_when_an_parameter_does_not_exist()
    {
        $this->expectException(Exception::class);

        $property = new TextPropertyType('NAME', 'Ruben');

        $property->addParameter(new Parameter('LASTNAME', 'Van Assche'));

        $property->getParameter('LASTNAM');
    }
}
