<?php

use Spatie\IcalendarGenerator\Components\Alert;
use Spatie\IcalendarGenerator\Exceptions\InvalidComponent;
use Spatie\IcalendarGenerator\Properties\TextProperty;
use Spatie\IcalendarGenerator\Tests\PayloadExpectation;
use Spatie\IcalendarGenerator\Tests\TestClasses\DummyComponent;

test('it will check if al required properties are set', function () {
    $dummy = new DummyComponent('Dummy');

    $payloadString = $dummy->toString();

    $this->assertNotNull($payloadString);
});

test('it will throw an exception when a required property is not set', function () {
    $dummy = new DummyComponent('Dummy');

    $dummy->name = null;

    $dummy->toString();
})->throws(InvalidComponent::class);

test('it will throw an exception when a required property is not set but another is', function () {
    $dummy = new DummyComponent('Dummy');

    $dummy->name = null;
    $dummy->description = 'Hello there';

    $dummy->toString();
})->throws(InvalidComponent::class);

test('it can add an extra property', function () {
    $dummy = new DummyComponent('Dummy');

    $dummy->appendProperty(
        TextProperty::create('organizer', 'ruben@spatie.be')
    );

    PayloadExpectation::create($dummy->resolvePayload())
        ->expectPropertyValue('organizer', 'ruben@spatie.be');
});

test('it can add an extra sub-component', function () {
    $dummy = new DummyComponent('Dummy');

    $component = Alert::minutesBeforeEnd(10);

    $dummy->appendSubComponent($component);

    PayloadExpectation::create($dummy->resolvePayload())
        ->expectSubComponentCount(1)
        ->expectSubComponents($component);
});
