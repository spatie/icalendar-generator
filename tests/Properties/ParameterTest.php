<?php

use function PHPUnit\Framework\assertEquals;
use Spatie\IcalendarGenerator\Enums\EventStatus;
use Spatie\IcalendarGenerator\Properties\Parameter;

use Spatie\IcalendarGenerator\ValueObjects\DateTimeValue;

test('it replaces all illegal characters', function () {
    assertEquals(
        'a backslash \\\\ ',
        (new Parameter('', 'a backslash \ '))->getValue()
    );

    assertEquals(
        'a quote ^\' ',
        (new Parameter('', 'a quote " '))->getValue()
    );

    assertEquals(
        'a comma \\, ',
        (new Parameter('', 'a comma , '))->getValue()
    );

    assertEquals(
        'a point-comma \\; ',
        (new Parameter('', 'a point-comma ; '))->getValue()
    );

    assertEquals(
        'a return ^n ',
        (new Parameter('', 'a return '.PHP_EOL.' '))->getValue()
    );

    assertEquals(
        'a circumflex accent ^^ ',
        (new Parameter('', 'a circumflex accent ^ '))->getValue()
    );
});

test('it can disable escaping', function () {
    assertEquals(
        'a backslash \ ',
        (new Parameter('', 'a backslash \ ', true))->getValue()
    );

    assertEquals(
        'a quote " ',
        (new Parameter('', 'a quote " ', true))->getValue()
    );

    assertEquals(
        'a comma , ',
        (new Parameter('', 'a comma , ', true))->getValue()
    );

    assertEquals(
        'a point-comma ; ',
        (new Parameter('', 'a point-comma ; ', true))->getValue()
    );

    assertEquals(
        'a return \n ',
        (new Parameter('', 'a return \n ', true))->getValue()
    );

    assertEquals(
        'a return ^ ',
        (new Parameter('', 'a return ^ ', true))->getValue()
    );
});

test('it can format a boolean', function () {
    assertEquals(
        'BOOLEAN:TRUE',
        (new Parameter('', true))->getValue()
    );

    assertEquals(
        'BOOLEAN:FALSE',
        (new Parameter('', false))->getValue()
    );
});

test('it can format an enum', function () {
    assertEquals(
        'CANCELLED',
        (new Parameter('', EventStatus::cancelled()))->getValue()
    );
});

test('it can format a DateTime value', function () {
    $dateTime = new DateTime('16 may 1994');

    assertEquals(
        'DATE-TIME:19940516T000000',
        (new Parameter('', DateTimeValue::create($dateTime)))->getValue()
    );

    assertEquals(
        'DATE:19940516',
        (new Parameter('', DateTimeValue::create($dateTime, false)))->getValue()
    );
});
