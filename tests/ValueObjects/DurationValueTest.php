<?php

use function PHPUnit\Framework\assertEquals;

use Spatie\IcalendarGenerator\ValueObjects\DurationValue;

test('it can create a duration property type', function () {
    $value = DurationValue::create(new DateInterval('PT5M'));

    assertEquals('PT5M', $value->format());
});

test('it can invert a duration property type', function () {
    $value = DurationValue::create(new DateInterval('PT5M'))->invert();

    assertEquals('-PT5M', $value->format());
});

test('it can create a duration property with all properties', function () {
    $value = DurationValue::create(new DateInterval('P4DT3H2M1S'));

    assertEquals('P4DT3H2M1S', $value->format());
});

test('it can create 0 seconds duration', function () {
    $value = DurationValue::create('PT0S');

    assertEquals('PT0S', $value->format());
});

test('it can use a regular string as duration', function () {
    $value = DurationValue::create('PT5M');

    assertEquals('PT5M', $value->format());
});
