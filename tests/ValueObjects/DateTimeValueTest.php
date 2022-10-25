<?php

use function PHPUnit\Framework\assertEquals;

use Spatie\IcalendarGenerator\ValueObjects\DateTimeValue;

test('it can update the timezone of a DateTime', function () {
    $datetime = new DateTime('16 may 2020 12:00:00', new DateTimeZone('Europe/Brussels'));

    $value = DateTimeValue::create($datetime)->convertToTimezone(
        new DateTimeZone('UTC')
    );

    assertEquals(
        '20200516T100000',
        $value->format()
    );
});

test('it can update the timezone of a DateTime immutable', function () {
    $datetime = new DateTimeImmutable('16 may 2020 12:00:00', new DateTimeZone('Europe/Brussels'));

    $value = DateTimeValue::create($datetime)->convertToTimezone(
        new DateTimeZone('UTC')
    );

    assertEquals(
        '20200516T100000',
        $value->format()
    );
});
