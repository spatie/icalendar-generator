<?php

use Spatie\IcalendarGenerator\Properties\BinaryProperty;
use Spatie\IcalendarGenerator\Tests\PropertyExpectation;
use Spatie\IcalendarGenerator\ValueObjects\BinaryValue;

test('it can create a binary property type', function () {
    $fileString = file_get_contents('.gitignore');

    $property = new BinaryProperty(
        'ATTACH',
        new BinaryValue($fileString)
    );

    PropertyExpectation::create($property)
        ->expectName('ATTACH')
        ->expectOutput(base64_encode($fileString))
        ->expectParameterCount(2)
        ->expectParameterValue('ENCODING', 'BASE64')
        ->expectParameterValue('VALUE', 'BINARY');
});

test('it can set a media type', function () {
    $fileString = file_get_contents('.gitignore');

    $property = new BinaryProperty(
        'ATTACH',
        new BinaryValue($fileString, 'text/plain')
    );

    PropertyExpectation::create($property)
        ->expectName('ATTACH')
        ->expectOutput(base64_encode($fileString))
        ->expectParameterCount(3)
        ->expectParameterValue('FMTTYPE', 'text/plain')
        ->expectParameterValue('ENCODING', 'BASE64')
        ->expectParameterValue('VALUE', 'BINARY');
});

test('it can accept base64 encoded content', function () {
    $fileString = base64_encode(file_get_contents('.gitignore'));

    $property = new BinaryProperty(
        'ATTACH',
        new BinaryValue($fileString, 'text/plain', false)
    );

    PropertyExpectation::create($property)
        ->expectName('ATTACH')
        ->expectOutput($fileString)
        ->expectParameterCount(3)
        ->expectParameterValue('FMTTYPE', 'text/plain')
        ->expectParameterValue('ENCODING', 'BASE64')
        ->expectParameterValue('VALUE', 'BINARY');
});
