<?php

namespace Spatie\Calendar\Tests;

use Spatie\Calendar\Duration;

class DurationTest extends TestCase
{
    /** @test */
    public function it_can_create_a_duration_with_weeks()
    {
        $duration = Duration::create()->weeks(7);

        $this->assertEquals('P7W', $duration->build());
    }

    /** @test */
    public function it_can_create_a_duration_with_days_and_time()
    {
        $duration = Duration::create()
            ->days(15)
            ->hours(5)
            ->seconds(20);

        $this->assertEquals('P15DT5H0M20S', $duration->build());
    }

    /** @test */
    public function it_can_create_a_duration_with_time()
    {
        $duration = Duration::create()
            ->hours(5)
            ->minutes(3)
            ->seconds(20);

        $this->assertEquals('PT5H3M20S', $duration->build());
    }

    /** @test */
    public function it_can_create_a_duration_with_a_day()
    {
        $duration = Duration::create()
            ->days(10);

        $this->assertEquals('P10D', $duration->build());
    }
    
    /** @test */
    public function it_can_create_negative_durations()
    {
        $duration = Duration::create()
            ->days(15)
            ->hours(5)
            ->seconds(20)
            ->ago();

        $this->assertEquals('-P15DT5H0M20S', $duration->build());
    }
}
