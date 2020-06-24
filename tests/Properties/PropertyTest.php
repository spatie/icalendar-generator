<?php

namespace Spatie\IcalendarGenerator\Tests\Properties;

use Exception;
use Spatie\IcalendarGenerator\Properties\Parameter;
use Spatie\IcalendarGenerator\Properties\TextProperty;
use Spatie\IcalendarGenerator\Tests\TestCase;

class PropertyTest extends TestCase
{
    /** @test */
    public function a_property_can_give_a_specified_parameter()
    {
        $property = new TextProperty('NAME', 'Ruben');

        $property->addParameter(new Parameter('LASTNAME', 'Van Assche'));

        $parameter = $property->getParameter('LASTNAME');

        $this->assertEquals('Van Assche', $parameter->getValue());
    }

    /** @test */
    public function an_exception_will_be_thrown_when_an_parameter_does_not_exist()
    {
        $this->expectException(Exception::class);

        $property = new TextProperty('NAME', 'Ruben');

        $property->addParameter(new Parameter('LASTNAME', 'Van Assche'));

        $property->getParameter('LASTNAM');
    }
}
