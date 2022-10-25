<?php

namespace Spatie\IcalendarGenerator\Tests;

use DateTime;
use Exception;
use Spatie\IcalendarGenerator\ComponentPayload;
use Spatie\IcalendarGenerator\Properties\DateTimeProperty;
use Spatie\IcalendarGenerator\Properties\Parameter;
use Spatie\IcalendarGenerator\Properties\TextProperty;
use Spatie\IcalendarGenerator\Tests\TestClasses\DummyComponent;
use Spatie\IcalendarGenerator\Tests\TestClasses\DummyProperty;

use function PHPUnit\Framework\assertEquals;

test('a payload has a type', function () {
    $payload = (new ComponentPayload('TESTCOMPONENT'));

    assertEquals('TESTCOMPONENT', $payload->getType());
});

test('a payload includes properties', function () {
    $date = new DateTime();

    $payload = (new ComponentPayload('TESTCOMPONENT'))
        ->property(TextProperty::create('text', 'Some text here'))
        ->property(DateTimeProperty::fromDateTime('date', $date));

    assertEquals([
        TextProperty::create('text', 'Some text here'),
        DateTimeProperty::fromDateTime('date', $date),
    ], $payload->getProperties());
});

test('a payload includes sub-components', function () {
    $subComponents = [
        new DummyComponent('subComponent1'),
        new DummyComponent('subComponent1'),
    ];

    $payload = (new ComponentPayload('TESTCOMPONENT'))
        ->subComponent(...$subComponents);

    assertEquals($subComponents, $payload->getSubComponents());
});

test('an exception will be thrown when a property does not exist', function () {
    $payload = (new ComponentPayload('TESTCOMPONENT'));

    $payload->getProperty('text');
})->throws(Exception::class);

test('an optional will only be added when the condition is true', function () {
    $payload = (new ComponentPayload('TESTCOMPONENT'));

    $payload->optional(false, fn () => TextProperty::create('text', 'Some text here'));
    $payload->optional(true, fn () => TextProperty::create('text', 'Other text here'));

    PayloadExpectation::create($payload)->expectPropertyValue(
        'text',
        'Other text here'
    );
});

test('an optional will only be added when it has a value', function () {
    $payload = (new ComponentPayload('TESTCOMPONENT'));

    $payload->optional(null, fn () => TextProperty::create('text', 'Some text here'));
    $payload->optional('something', fn () => TextProperty::create('text', 'Other text here'));

    PayloadExpectation::create($payload)->expectPropertyValue(
        'text',
        'Other text here'
    );
});

test('a multiple will be added via closure', function () {
    $payload = (new ComponentPayload('TESTCOMPONENT'));

    $payload->multiple(['a', 'b', 'c'], fn (string $letter) => TextProperty::create('text', $letter));

    $this->assertEquals([
        TextProperty::create('text', 'a'),
        TextProperty::create('text', 'b'),
        TextProperty::create('text', 'c'),
    ], $payload->getProperties());
});

test('a property can be added with parameters', function () {
    $property = new DummyProperty('name', 'TESTPROPERTY');

    $parameters = [
        new Parameter('hello', 'world'),
    ];

    $payload = (new ComponentPayload('TESTCOMPONENT'))
        ->property($property, $parameters);

    assertEquals($parameters, $payload->getProperty('name')->getParameters());
});
