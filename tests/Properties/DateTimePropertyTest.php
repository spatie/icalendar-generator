<?php

use Spatie\IcalendarGenerator\Properties\DateTimeProperty;
use Spatie\IcalendarGenerator\Tests\PropertyExpectation;
use Spatie\IcalendarGenerator\ValueObjects\DateTimeValue;

beforeEach(function () {
    $this->date = (new DateTime('16 may 2019 12:10:15', new DateTimeZone('Europe/Brussels')));
});

test('it will format the date correctly', function () {
    $property = DateTimeProperty::fromDateTime('STARTS', $this->date);

    PropertyExpectation::create($property)->expectOutput('20190516');
});

test('it will format the date and time correctly with timezone', function () {
    $property = DateTimeProperty::fromDateTime('STARTS', $this->date, true);

    PropertyExpectation::create($property)
        ->expectOutput('20190516T121015')
        ->expectParameterCount(1)
        ->expectParameterValue('TZID', 'Europe/Brussels');
});

test('it will use a specific UTC format', function () {
    $this->date->setTimezone(new DateTimeZone('UTC'));

    $property = DateTimeProperty::fromDateTime('STARTS', $this->date, true);

    PropertyExpectation::create($property)
        ->expectOutput('20190516T101015Z')
        ->expectParameterCount(0);
});

test('it will not use a specific UTC format when time is not given', function () {
    $this->date->setTimezone(new DateTimeZone('UTC'));

    $property = DateTimeProperty::fromDateTime('STARTS', $this->date);

    PropertyExpectation::create($property)
        ->expectOutput('20190516')
        ->expectParameterCount(1)
        ->expectParameterValue('VALUE', 'DATE');
});

test('it will use a non-UTC timezone format when time is not given', function () {
    $property = DateTimeProperty::fromDateTime('STARTS', $this->date, false);

    PropertyExpectation::create($property)
        ->expectOutput('20190516')
        ->expectParameterCount(2)
        ->expectParameterValue('TZID', 'Europe/Brussels')
        ->expectParameterValue('VALUE', 'DATE');
});

test('it will format the date and time correctly with a conversion to another timezone', function () {
    $this->date->setTimezone(new DateTimeZone('Europe/Brussels'));

    $property = DateTimeProperty::fromDateTime('STARTS', $this->date, true);

    PropertyExpectation::create($property)
        ->expectOutput('20190516T121015')
        ->expectParameterCount(1)
        ->expectParameterValue('TZID', 'Europe/Brussels');
});

test('it will format the date and time without timezone', function () {
    $property = DateTimeProperty::fromDateTime('STARTS', $this->date, true, true);

    PropertyExpectation::create($property)->expectOutput('20190516T121015');
});

test('it can be created from a DateTime value', function () {
    $property = DateTimeProperty::create(
        'STARTS',
        DateTimeValue::create($this->date)
    );

    PropertyExpectation::create($property)->expectOutput('20190516T121015');
});
