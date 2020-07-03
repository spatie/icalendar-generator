<?php

namespace Spatie\IcalendarGenerator\Tests\ValueObjects;

use DateInterval;
use DateTime;
use PHPUnit\Framework\TestCase;
use Spatie\IcalendarGenerator\ValueObjects\DurationValue;
use Spatie\IcalendarGenerator\ValueObjects\PeriodValue;

class PeriodValueTest extends TestCase
{
    /** @test */
    public function it_can_create_a_period_with_times()
    {
        $period = PeriodValue::create(
            new DateTime('16 may 2020 12:00:00'),
            new DateTime('18 may 2020 16:00:00')
        );

        $this->assertEquals('20200516T120000/20200518T160000', $period->format());
    }

    /** @test */
    public function it_can_create_a_period_with_time_and_duration()
    {
        $period = PeriodValue::create(
            new DateTime('16 may 2020 12:00:00'),
            DurationValue::create('PT5M')
        );

        $this->assertEquals('20200516T120000/PT5M', $period->format());
    }
}
