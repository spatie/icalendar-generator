<?php

use Spatie\IcalendarGenerator\Properties\Parameter;
use Spatie\IcalendarGenerator\Properties\TextProperty;

use function PHPUnit\Framework\assertEquals;

test('a property can give a specific parameter', function () {
    $property = new TextProperty('NAME', 'Ruben');

    $property->addParameter(new Parameter('LASTNAME', 'Van Assche'));

    $parameter = $property->getParameter('LASTNAME');

    assertEquals('Van Assche', $parameter->getValue());
});

test('an exception will be thrown when a parameter does not exist', function () {
    $property = new TextProperty('NAME', 'Ruben');

    $property->addParameter(new Parameter('LASTNAME', 'Van Assche'));

    $property->getParameter('LASTNAM');
})->throws(Exception::class);
