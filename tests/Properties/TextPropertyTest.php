<?php

use function PHPUnit\Framework\assertEquals;
use Spatie\IcalendarGenerator\Enums\Classification;
use Spatie\IcalendarGenerator\Properties\TextProperty;

use Spatie\IcalendarGenerator\Tests\PropertyExpectation;

test('it replaces all illegal characters', function () {
    assertEquals(
        'a backslash \\\\ ',
        (new TextProperty('', 'a backslash \ '))->getValue()
    );

    assertEquals(
        'a quote \\" ',
        (new TextProperty('', 'a quote " '))->getValue()
    );

    assertEquals(
        'a comma \\, ',
        (new TextProperty('', 'a comma , '))->getValue()
    );

    assertEquals(
        'a point-comma \\; ',
        (new TextProperty('', 'a point-comma ; '))->getValue()
    );

    assertEquals(
        'a return \\\n ',
        (new TextProperty('', 'a return \n '))->getValue()
    );
});

test('it can disable escaping', function () {
    assertEquals(
        'a backslash \ ',
        (new TextProperty('', 'a backslash \ '))->withoutEscaping()->getValue()
    );

    assertEquals(
        'a quote " ',
        (new TextProperty('', 'a quote " '))->withoutEscaping()->getValue()
    );

    assertEquals(
        'a comma , ',
        (new TextProperty('', 'a comma , '))->withoutEscaping()->getValue()
    );

    assertEquals(
        'a point-comma ; ',
        (new TextProperty('', 'a point-comma ; '))->withoutEscaping()->getValue()
    );

    assertEquals(
        'a return \n ',
        (new TextProperty('', 'a return \n '))->withoutEscaping()->getValue()
    );
});

test('it can be created from an enum', function () {
    $property = TextProperty::createFromEnum('', Classification::Private);

    PropertyExpectation::create($property)
        ->expectOutput('PRIVATE')
        ->expectValue('PRIVATE');
});

test('it normalizes all newline variants before escaping', function () {
    // LF (Unix)
    assertEquals(
        'line1\\nline2',
        (new TextProperty('', "line1\nline2"))->getValue()
    );

    // CRLF (Windows)
    assertEquals(
        'line1\\nline2',
        (new TextProperty('', "line1\r\nline2"))->getValue()
    );

    // CR (old Mac)
    assertEquals(
        'line1\\nline2',
        (new TextProperty('', "line1\rline2"))->getValue()
    );

    // Mixed newlines
    assertEquals(
        'line1\\nline2\\nline3\\nline4',
        (new TextProperty('', "line1\nline2\r\nline3\rline4"))->getValue()
    );
});
