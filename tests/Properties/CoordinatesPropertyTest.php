<?php

use Spatie\IcalendarGenerator\Properties\CoordinatesProperty;
use Spatie\IcalendarGenerator\Tests\PropertyExpectation;

test('it can create a coordinates property type', function () {
    $propertyType = new CoordinatesProperty('GEO', 10.5, 20.5);

    PropertyExpectation::create($propertyType)
        ->expectName('GEO')
        ->expectOutput('10.5;20.5')
        ->expectValue(['lat' => 10.5, 'lng' => 20.5]);
});
