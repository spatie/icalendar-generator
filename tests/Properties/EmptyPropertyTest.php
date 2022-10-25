<?php

use function PHPUnit\Framework\assertEquals;
use Spatie\IcalendarGenerator\Properties\EmptyProperty;

use Spatie\IcalendarGenerator\Tests\PropertyExpectation;

test('it can create an empty property type', function () {
    $propertyType = new EmptyProperty('CONTACT', []);

    assertEquals('CONTACT', $propertyType->getName());
    assertEquals(null, $propertyType->getValue());
    assertEquals(null, $propertyType->getOriginalValue());

    PropertyExpectation::create($propertyType)
        ->expectName('CONTACT')
        ->expectValue(null)
        ->expectOutput(null);
});
