<?php

use function PHPUnit\Framework\assertEquals;
use Spatie\IcalendarGenerator\Properties\Parameter;

use Spatie\IcalendarGenerator\Properties\TextProperty;

beforeEach(function () {
    $this->property = new TextProperty('NAME', 'Ruben');

    $this->property->addParameter(new Parameter('LASTNAME', 'Van Assche'));
});

test('a property can give a specific parameter', function () {
    $parameter = $this->property->getParameter('LASTNAME');

    assertEquals('Van Assche', $parameter->getValue());
});

test('an exception will be thrown when a parameter does not exist', function () {
    $this->property->getParameter('LASTNAM');
})->throws(Exception::class);
