<?php

namespace Spatie\IcalendarGenerator\Tests\Properties;

use Spatie\IcalendarGenerator\Properties\CoordinatesProperty;
use Spatie\IcalendarGenerator\Tests\PropertyExpectation;

test('it can create a coordinates property type', function () {
    $propertyType = new CoordinatesProperty('GEO', 10.5, 20.5);

    PropertyExpectation::create($propertyType)
        ->expectName('GEO')
        ->expectOutput('10.5;20.5')
        ->expectValue(['lat' => 10.5, 'lng' => 20.5]);
});

test('it_has_dot_as_decimal_point', function () {
    setlocale(LC_ALL, 'de_DE.UTF-8');
    $propertyType = new CoordinatesProperty('GEO', 10.5, 20.5);

    PropertyExpectation::create($propertyType)
        ->expectName('GEO')
        ->expectOutput('10.5;20.5')
        ->expectValue(['lat' => 10.5, 'lng' => 20.5]);
});
