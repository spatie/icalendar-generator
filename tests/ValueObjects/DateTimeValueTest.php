<?php

namespace Spatie\IcalendarGenerator\Tests\ValueObjects;

use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use Spatie\IcalendarGenerator\Tests\TestCase;
use Spatie\IcalendarGenerator\ValueObjects\DateTimeValue;

class DateTimeValueTest extends TestCase
{
    /** @test */
    public function it_can_update_the_timezone_of_a_datetime()
    {
        $datetime = new DateTime('16 may 2020 12:00:00', new DateTimeZone('Europe/Brussels'));

        $value = DateTimeValue::create($datetime)->convertToTimezone(
            new DateTimeZone('UTC')
        );

        $this->assertEquals(
            '20200516T100000',
            $value->format()
        );
    }

    /** @test */
    public function it_can_update_the_timezone_of_a_datetime_immutable()
    {
        $datetime = new DateTimeImmutable('16 may 2020 12:00:00', new DateTimeZone('Europe/Brussels'));

        $value = DateTimeValue::create($datetime)->convertToTimezone(
            new DateTimeZone('UTC')
        );

        $this->assertEquals(
            '20200516T100000',
            $value->format()
        );
    }
}
