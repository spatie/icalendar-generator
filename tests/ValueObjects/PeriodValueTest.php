<?php

use Spatie\IcalendarGenerator\ValueObjects\DurationValue;
use Spatie\IcalendarGenerator\ValueObjects\PeriodValue;

use function PHPUnit\Framework\assertEquals;

test('it can create a period with times', function () {
    $period = PeriodValue::create(
        new DateTime('16 may 2020 12:00:00'),
        new DateTime('18 may 2020 16:00:00')
    );

    assertEquals('20200516T120000/20200518T160000', $period->format());
});

test('it can create a period with time and duration', function () {
    $period = PeriodValue::create(
        new DateTime('16 may 2020 12:00:00'),
        DurationValue::create('PT5M')
    );

    assertEquals('20200516T120000/PT5M', $period->format());
});
