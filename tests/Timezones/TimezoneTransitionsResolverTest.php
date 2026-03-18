<?php

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotEquals;
use Spatie\IcalendarGenerator\Enums\TimezoneEntryType;

use Spatie\IcalendarGenerator\Timezones\TimezoneTransitionsResolver;

test('it gets the correct timezone transitions', function () {
    $resolver = new TimezoneTransitionsResolver(
        new DateTimeZone('America/New_York'),
        new DateTime('1967-01-01'),
        new DateTime()
    );

    // Cases from https://tools.ietf.org/html/rfc5545#section-3.6.5
    $transitions = $resolver->getTransitions();

    /** @var \Spatie\IcalendarGenerator\Timezones\TimezoneTransition $first */
    $transition = $transitions[2];
    assertEquals('1967-04-30T02:00:00', $transition->start->format('Y-m-d\TH:i:s'));
    assertEquals(TimezoneEntryType::Daylight, $transition->type);
    assertEquals(createOffset(5, 0, true), $transition->offsetFrom);
    assertEquals(createOffset(4, 0, true), $transition->offsetTo);

    /** @var \Spatie\IcalendarGenerator\Timezones\TimezoneTransition $first */
    $transition = $transitions[3];
    assertEquals('1967-10-29T02:00:00', $transition->start->format('Y-m-d\TH:i:s'));
    assertEquals(TimezoneEntryType::Standard, $transition->type);
    assertEquals(createOffset(4, 0, true), $transition->offsetFrom);
    assertEquals(createOffset(5, 0, true), $transition->offsetTo);

    /** @var \Spatie\IcalendarGenerator\Timezones\TimezoneTransition $first */
    $transition = $transitions[16];
    assertEquals('1974-01-06T02:00:00', $transition->start->format('Y-m-d\TH:i:s'));
    assertEquals(TimezoneEntryType::Daylight, $transition->type);
    assertEquals(createOffset(5, 0, true), $transition->offsetFrom);
    assertEquals(createOffset(4, 0, true), $transition->offsetTo);

    /** @var \Spatie\IcalendarGenerator\Timezones\TimezoneTransition $first */
    $transition = $transitions[20];
    assertEquals('1976-04-25T02:00:00', $transition->start->format('Y-m-d\TH:i:s'));
    assertEquals(TimezoneEntryType::Daylight, $transition->type);
    assertEquals(createOffset(5, 0, true), $transition->offsetFrom);
    assertEquals(createOffset(4, 0, true), $transition->offsetTo);
});

test('it gets the correct timezone transitions for positive offsets', function () {
    $resolver = new TimezoneTransitionsResolver(
        new DateTimeZone('Europe/Brussels'),
        new DateTime('2000-01-01'),
        new DateTime()
    );

    $transitions = $resolver->getTransitions();

    /** @var \Spatie\IcalendarGenerator\Timezones\TimezoneTransition $first */
    $transition = $transitions[1];
    assertEquals('2000-03-26T02:00:00', $transition->start->format('Y-m-d\TH:i:s'));
    assertEquals(TimezoneEntryType::Daylight, $transition->type);
    assertEquals(createOffset(1, 0), $transition->offsetFrom);
    assertEquals(createOffset(2, 0), $transition->offsetTo);

    /** @var \Spatie\IcalendarGenerator\Timezones\TimezoneTransition $first */
    $transition = $transitions[2];
    assertEquals('2000-10-29T03:00:00', $transition->start->format('Y-m-d\TH:i:s'));
    assertEquals(TimezoneEntryType::Standard, $transition->type);
    assertEquals(createOffset(2, 0), $transition->offsetFrom);
    assertEquals(createOffset(1, 0), $transition->offsetTo);
});

test('it can work with funny timezones', function () {
    $resolver = new TimezoneTransitionsResolver(
        new DateTimeZone('Pacific/Chatham'),
        new DateTime('2000-01-01'),
        new DateTime()
    );

    $transitions = $resolver->getTransitions();

    /** @var \Spatie\IcalendarGenerator\Timezones\TimezoneTransition $first */
    $transition = $transitions[1];
    assertEquals('2000-03-19T03:45:00', $transition->start->format('Y-m-d\TH:i:s'));
    assertEquals(TimezoneEntryType::Standard, $transition->type);
    assertEquals(createOffset(13, 45), $transition->offsetFrom);
    assertEquals(createOffset(12, 45), $transition->offsetTo);

    /** @var \Spatie\IcalendarGenerator\Timezones\TimezoneTransition $first */
    $transition = $transitions[2];
    assertEquals('2000-10-01T02:45:00', $transition->start->format('Y-m-d\TH:i:s'));
    assertEquals(TimezoneEntryType::Daylight, $transition->type);
    assertEquals(createOffset(12, 45), $transition->offsetFrom);
    assertEquals(createOffset(13, 45), $transition->offsetTo);
});

test('it can use UTC as timezone', function () {
    $resolver = new TimezoneTransitionsResolver(
        new DateTimeZone('UTC'),
        new DateTime('2000-01-01'),
        new DateTime()
    );

    $transitions = $resolver->getTransitions();

    /** @var \Spatie\IcalendarGenerator\Timezones\TimezoneTransition $first */
    $transition = $transitions[0];
    assertEquals('1999-04-06T00:00:00', $transition->start->format('Y-m-d\TH:i:s'));
    assertEquals(TimezoneEntryType::Standard, $transition->type);
    assertEquals(createOffset(0, 0), $transition->offsetFrom);
    assertEquals(createOffset(0, 0), $transition->offsetTo);
});

test('transition start datetime is not in UTC to prevent Z suffix in VTIMEZONE DTSTART', function () {
    $resolver = new TimezoneTransitionsResolver(
        new DateTimeZone('Europe/Brussels'),
        new DateTime('2000-01-01'),
        new DateTime()
    );

    $transitions = $resolver->getTransitions();

    foreach ($transitions as $transition) {
        assertNotEquals(
            'UTC',
            $transition->start->getTimezone()->getName(),
            'VTIMEZONE DTSTART must be local date-time per RFC 5545 §3.6.5, DateTime must not carry UTC timezone'
        );
    }
});
