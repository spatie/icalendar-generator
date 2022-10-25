<?php

use Spatie\IcalendarGenerator\Properties\EmptyProperty;
use Spatie\IcalendarGenerator\Tests\PropertyExpectation;

test('it can create an empty property type', function () {
    $propertyType = new EmptyProperty('CONTACT', []);

    $this->assertEquals('CONTACT', $propertyType->getName());
    $this->assertEquals(null, $propertyType->getValue());
    $this->assertEquals(null, $propertyType->getOriginalValue());

    PropertyExpectation::create($propertyType)
        ->expectName('CONTACT')
        ->expectValue(null)
        ->expectOutput(null);
});
