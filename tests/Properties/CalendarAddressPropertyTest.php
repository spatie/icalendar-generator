<?php

use Spatie\IcalendarGenerator\Enums\ParticipationStatus;
use Spatie\IcalendarGenerator\Properties\CalendarAddressProperty;
use Spatie\IcalendarGenerator\Tests\PropertyExpectation;
use Spatie\IcalendarGenerator\ValueObjects\CalendarAddress;

test('it can create a calendar property type', function () {
    $property = new CalendarAddressProperty(
        'ORGANIZER',
        new CalendarAddress('ruben@spatie.be')
    );

    PropertyExpectation::create($property)
        ->expectName('ORGANIZER')
        ->expectOutput('MAILTO:ruben@spatie.be')
        ->expectParameterCount(0);
});

test('it can set a name and participation status', function () {
    $property = new CalendarAddressProperty(
        'ORGANIZER',
        new CalendarAddress('ruben@spatie.be', 'Ruben', ParticipationStatus::accepted())
    );

    PropertyExpectation::create($property)
        ->expectName('ORGANIZER')
        ->expectOutput('MAILTO:ruben@spatie.be')
        ->expectParameterCount(2)
        ->expectParameterValue('CN', 'Ruben')
        ->expectParameterValue('PARTSTAT', ParticipationStatus::accepted()->value);
});

test('it can set RSVP to true', function () {
    $property = new CalendarAddressProperty(
        'ATTENDEE',
        new CalendarAddress('ruben@spatie.be', 'Ruben', ParticipationStatus::needs_action(), true)
    );

    PropertyExpectation::create($property)
        ->expectName('ATTENDEE')
        ->expectOutput('MAILTO:ruben@spatie.be')
        ->expectParameterCount(3)
        ->expectParameterValue('CN', 'Ruben')
        ->expectParameterValue('RSVP', 'TRUE')
        ->expectParameterValue('PARTSTAT', ParticipationStatus::needs_action()->value);
});
