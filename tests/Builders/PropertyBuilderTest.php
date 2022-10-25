<?php

use Spatie\IcalendarGenerator\Builders\PropertyBuilder;
use Spatie\IcalendarGenerator\Properties\EmptyProperty;
use Spatie\IcalendarGenerator\Properties\Parameter;
use Spatie\IcalendarGenerator\Properties\TextProperty;
use Spatie\IcalendarGenerator\Tests\TestClasses\DummyProperty;

use function PHPUnit\Framework\assertEquals;

test('it will build the property correctly', function () {
    $property = new DummyProperty('location', 'Antwerp');

    assertEquals(
        ['location:Antwerp'],
        (new PropertyBuilder($property))->build()
    );
});

test('it will build the parameters correctly', function () {
    $property = new DummyProperty('location', 'Antwerp');

    $property->addParameter(
        new Parameter('street', 'Samberstraat')
    );

    assertEquals(
        ['location;street=Samberstraat:Antwerp'],
        (new PropertyBuilder($property))->build()
    );
});

test('it will build the property according to specific rules', function () {
    $property = new TextProperty('location', 'Antwerp, Belgium');

    assertEquals(
        ['location:Antwerp\, Belgium'],
        (new PropertyBuilder($property))->build()
    );
});

test('it will use the alias of a property when given', function () {
    $property = TextProperty::create('location', 'Antwerp, Belgium')->addAlias('geo');

    assertEquals(
        [
            'location:Antwerp\, Belgium',
            'geo:Antwerp\, Belgium',
        ],
        (new PropertyBuilder($property))->build()
    );
});

test('it can build properties without value', function () {
    $property = new EmptyProperty('contact', [Parameter::create('NON-SMOKER', true)]);

    assertEquals(
        ['contact;NON-SMOKER=BOOLEAN:TRUE'],
        (new PropertyBuilder($property))->build()
    );
});
