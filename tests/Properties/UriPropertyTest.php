<?php

use Spatie\IcalendarGenerator\Properties\UriProperty;
use Spatie\IcalendarGenerator\Tests\PropertyExpectation;

test('it accepts an URI', function () {
    PropertyExpectation::create(new UriProperty('', 'http://this.is/a/valid/uri'))
        ->expectValue('http://this.is/a/valid/uri');

    PropertyExpectation::create(new UriProperty('', 'foo://example.com:8042/over/there?name=ferret#nose'))
        ->expectValue('foo://example.com:8042/over/there?name=ferret#nose');
});

test('it will return an empty string if the URI is not valid', function () {
    PropertyExpectation::create(new UriProperty('', 'i-am-not-valid'))->expectOutput('');
});
