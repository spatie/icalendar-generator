<?php

use function PHPUnit\Framework\assertEquals;
use Spatie\IcalendarGenerator\Enums\RecurrenceDay;
use Spatie\IcalendarGenerator\Enums\RecurrenceFrequency;

use Spatie\IcalendarGenerator\ValueObjects\RRule;

test('it can create a rrule', function () {
    $rrule = RRule::frequency(RecurrenceFrequency::Daily)->compose();

    assertEquals([
        "FREQ" => "DAILY",
    ], $rrule);
});

test('it can set the start date', function () {
    $rrule = RRule::frequency(RecurrenceFrequency::Daily)
        ->starting(new DateTime('16 may 1994'))
        ->compose();

    assertEquals([
        "FREQ" => "DAILY",
        'DTSTART' => '19940516T000000',
    ], $rrule);
});

test('it can set until', function () {
    $rrule = RRule::frequency(RecurrenceFrequency::Daily)
        ->until(new DateTime('16 may 1994'))
        ->compose();

    assertEquals([
        "FREQ" => "DAILY",
        'UNTIL' => '19940516T000000',
    ], $rrule);
});

test('it can set count', function () {
    $rrule = RRule::frequency(RecurrenceFrequency::Daily)
        ->times(10)
        ->compose();

    assertEquals([
        "FREQ" => "DAILY",
        'COUNT' => '10',
    ], $rrule);
});

test('it cannot set a negative count', function () {
    RRule::frequency(RecurrenceFrequency::Daily)
        ->times(-1)
        ->compose();
})->throws(Exception::class);

test('it can set interval', function () {
    $rrule = RRule::frequency(RecurrenceFrequency::Daily)
        ->interval(10)
        ->compose();

    assertEquals([
        "FREQ" => "DAILY",
        'INTERVAL' => '10',
    ], $rrule);
});

test('it cannot set a negative interval', function () {
    RRule::frequency(RecurrenceFrequency::Daily)
        ->interval(-1)
        ->compose();
})->throws(Exception::class);

test('it can set the week starts on', function () {
    $rrule = RRule::frequency(RecurrenceFrequency::Daily)
        ->weekStartsOn(RecurrenceDay::Monday)
        ->compose();

    assertEquals([
        "FREQ" => "DAILY",
        'WKST' => 'MO',
    ], $rrule);
});

test('it can add week days', function (array $days, string $expected) {
    $rrule = RRule::frequency(RecurrenceFrequency::Daily);

    foreach ($days as $day) {
        $rrule->onWeekDay($day['day'], $day['index']);
    }

    assertEquals([
        "FREQ" => "DAILY",
        'BYDAY' => $expected,
    ], $rrule->compose());
})->with('week-days');

test('it can add months', function ($months, string $expected) {
    $rrule = RRule::frequency(RecurrenceFrequency::Daily)
        ->onMonth($months)
        ->compose();

    assertEquals([
        "FREQ" => "DAILY",
        'BYMONTH' => $expected,
    ], $rrule);
})->with('months');

test('it can add month days', function ($monthDays, string $expected) {
    $rrule = RRule::frequency(RecurrenceFrequency::Daily)
        ->onMonthDay($monthDays)
        ->compose();

    assertEquals([
        "FREQ" => "DAILY",
        'BYMONTHDAY' => $expected,
    ], $rrule);
})->with('month-days');
