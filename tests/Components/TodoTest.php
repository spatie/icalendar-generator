<?php

use Spatie\IcalendarGenerator\Components\Todo;
use Spatie\IcalendarGenerator\ValueObjects\CalendarAddress;
use Spatie\IcalendarGenerator\Enums\Display;
use Spatie\IcalendarGenerator\Enums\ParticipationStatus;
use Spatie\IcalendarGenerator\ValueObjects\RRule;
use Spatie\IcalendarGenerator\Enums\RecurrenceFrequency;
use Spatie\IcalendarGenerator\Properties\DateTimeProperty;
use Spatie\IcalendarGenerator\Tests\PayloadExpectation;
use Spatie\IcalendarGenerator\Tests\PropertyExpectation;

test('it can create a todo', function () {
    $payload = Todo::create()->resolvePayload();

    PayloadExpectation::create($payload)
        ->expectType('VTODO')
        ->expectPropertyCount(2)
        ->expectPropertyExists('UID')
        ->expectPropertyExists('DTSTAMP');
});

test('it can set properties on a todo', function () {
    $dateCreated = new DateTime('16 may 2019');
    $dateDue = new DateTime('18 may 2019 10:00:00');

    $payload = Todo::create('Finish report')
        ->description('Write the Q2 report')
        ->createdAt($dateCreated)
        ->url('http://example.com/todos/1.ics')
        ->uniqueIdentifier('todo-identifier')
        ->dueAt($dateDue)
        ->resolvePayload();

    PayloadExpectation::create($payload)
        ->expectPropertyValue('SUMMARY', 'Finish report')
        ->expectPropertyValue('DESCRIPTION', 'Write the Q2 report')
        ->expectPropertyValue('DTSTAMP', $dateCreated)
        ->expectPropertyValue('DUE', $dateDue)
        ->expectPropertyValue('UID', 'todo-identifier')
        ->expectPropertyValue('URL', 'http://example.com/todos/1.ics');
});

test('it can add an organizer and attendees to a todo', function () {
    $payload = Todo::create()
        ->organizer('owner@example.com', 'Owner')
        ->attendee('alice@example.com', 'Alice', ParticipationStatus::Accepted)
        ->attendee('bob@example.com', 'Bob')
        ->resolvePayload();

    PropertyExpectation::create($payload, 'ORGANIZER')
        ->expectValue(new CalendarAddress('owner@example.com', 'Owner'));

    PayloadExpectation::create($payload)
        ->expectPropertyValue(
            'ATTENDEE',
            new CalendarAddress('alice@example.com', 'Alice', ParticipationStatus::Accepted),
            new CalendarAddress('bob@example.com', 'Bob')
        );
});

test('it can add recurrence rules and dates to a todo', function () {
    $rrule = RRule::frequency(RecurrenceFrequency::Daily);
    $dateA = new DateTime('16 may 2019 09:00:00');
    $dateB = new DateTime('16 may 2020 09:00:00');

    $payload = Todo::create('Recurring task')
        ->rrule($rrule)
        ->repeatOn($dateA)
        ->doNotRepeatOn($dateB)
        ->resolvePayload();

    PropertyExpectation::create($payload, 'RRULE')
        ->expectValue($rrule);

    PropertyExpectation::create($payload, 'RDATE')
        ->expectInstanceOf(DateTimeProperty::class)
        ->expectValue($dateA);

    PropertyExpectation::create($payload, 'EXDATE')
        ->expectInstanceOf(DateTimeProperty::class)
        ->expectValue($dateB);
});

test('it can add attachments to a todo', function () {
    $payload = Todo::create()
        ->attachment('http://example.com/file.pdf')
        ->attachment('http://example.com/file.json', 'application/json')
        ->resolvePayload();

    PayloadExpectation::create($payload)->expectProperty(
        'ATTACH',
        fn (PropertyExpectation $expectation) => $expectation
            ->expectParameterCount(0)
            ->expectValue('http://example.com/file.pdf'),
        fn (PropertyExpectation $expectation) => $expectation
            ->expectParameterCount(1)
            ->expectParameterValue('FMTTYPE', 'application/json')
            ->expectValue('http://example.com/file.json')
    );
});

test('it can add a sequence to a todo', function () {
    $payload = Todo::create('A todo')
        ->sequence(2)
        ->resolvePayload();

    PayloadExpectation::create($payload)
        ->expectPropertyValue('SEQUENCE', 2);
});
