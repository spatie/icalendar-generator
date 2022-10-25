<?php

use Spatie\IcalendarGenerator\Properties\DurationProperty;
use Spatie\IcalendarGenerator\Tests\PropertyExpectation;

test('it can create a duration property type', function () {
    $interval = new DateInterval('PT5M');

    $property = new DurationProperty('DURATION', $interval);

    PropertyExpectation::create($property)
        ->expectName('DURATION')
        ->expectValue($interval)
        ->expectOutput('PT5M');
});
