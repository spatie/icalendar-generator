<?php

namespace Spatie\IcalendarGenerator\Tests\Timezones;

use DateInterval;
use DateTime;
use DateTimeZone;
use Spatie\IcalendarGenerator\Enums\TimezoneEntryType;
use Spatie\IcalendarGenerator\Tests\TestCase;
use Spatie\IcalendarGenerator\Timezones\TimezoneTransition;
use Spatie\IcalendarGenerator\Timezones\TimezoneTransitionsResolver;

class TimezoneTransitionsResolverTest extends TestCase
{
    /** @test */
    public function it_gets_the_correct_timezone_transitions()
    {
        $resolver = new TimezoneTransitionsResolver(
            new DateTimeZone('America/New_York'),
            new DateTime('1967-01-01'),
            new DateTime()
        );

        // Cases from https://tools.ietf.org/html/rfc5545#section-3.6.5
        $transitions = $resolver->getTransitions();

        /** @var \Spatie\IcalendarGenerator\Timezones\TimezoneTransition $first */
        $transition = $transitions[2];
        $this->assertEquals(new DateTime('1967-04-30T02:00:00+00:00'), $transition->start);
        $this->assertEquals(TimezoneEntryType::daylight(), $transition->type);
        $this->assertEquals($this->createOffset(5, 0, true), $transition->offsetFrom);
        $this->assertEquals($this->createOffset(4, 0, true), $transition->offsetTo);

        /** @var \Spatie\IcalendarGenerator\Timezones\TimezoneTransition $first */
        $transition = $transitions[3];
        $this->assertEquals(new DateTime('1967-10-29T02:00:00+00:00'), $transition->start);
        $this->assertEquals(TimezoneEntryType::standard(), $transition->type);
        $this->assertEquals($this->createOffset(4, 0, true), $transition->offsetFrom);
        $this->assertEquals($this->createOffset(5, 0, true), $transition->offsetTo);

        /** @var \Spatie\IcalendarGenerator\Timezones\TimezoneTransition $first */
        $transition = $transitions[16];
        $this->assertEquals(new DateTime('1974-01-06T02:00:00+00:00'), $transition->start);
        $this->assertEquals(TimezoneEntryType::daylight(), $transition->type);
        $this->assertEquals($this->createOffset(5, 0, true), $transition->offsetFrom);
        $this->assertEquals($this->createOffset(4, 0, true), $transition->offsetTo);

        /** @var \Spatie\IcalendarGenerator\Timezones\TimezoneTransition $first */
        $transition = $transitions[20];
        $this->assertEquals(new DateTime('1976-04-25T02:00:00+00:00'), $transition->start);
        $this->assertEquals(TimezoneEntryType::daylight(), $transition->type);
        $this->assertEquals($this->createOffset(5, 0, true), $transition->offsetFrom);
        $this->assertEquals($this->createOffset(4, 0, true), $transition->offsetTo);
    }

    /** @test */
    public function it_gets_the_correct_timezone_transitions_for_positive_offsets()
    {
        $resolver = new TimezoneTransitionsResolver(
            new DateTimeZone('Europe/Brussels'),
            new DateTime('2000-01-01'),
            new DateTime()
        );

        $transitions = $resolver->getTransitions();

        /** @var \Spatie\IcalendarGenerator\Timezones\TimezoneTransition $first */
        $transition = $transitions[1];
        $this->assertEquals(new DateTime('2000-03-26T02:00:00+00:00'), $transition->start);
        $this->assertEquals(TimezoneEntryType::daylight(), $transition->type);
        $this->assertEquals($this->createOffset(1, 0), $transition->offsetFrom);
        $this->assertEquals($this->createOffset(2, 0), $transition->offsetTo);

        /** @var \Spatie\IcalendarGenerator\Timezones\TimezoneTransition $first */
        $transition = $transitions[2];
        $this->assertEquals(new DateTime('2000-10-29T03:00:00+00:00'), $transition->start);
        $this->assertEquals(TimezoneEntryType::standard(), $transition->type);
        $this->assertEquals($this->createOffset(2, 0), $transition->offsetFrom);
        $this->assertEquals($this->createOffset(1, 0), $transition->offsetTo);
    }

    /** @test */
    public function it_can_work_with_funny_timezones()
    {
        $resolver = new TimezoneTransitionsResolver(
            new DateTimeZone('Pacific/Chatham'),
            new DateTime('2000-01-01'),
            new DateTime()
        );

        $transitions = $resolver->getTransitions();

        /** @var \Spatie\IcalendarGenerator\Timezones\TimezoneTransition $first */
        $transition = $transitions[1];
        $this->assertEquals(new DateTime('2000-03-19T03:45:00+00:00'), $transition->start);
        $this->assertEquals(TimezoneEntryType::standard(), $transition->type);
        $this->assertEquals($this->createOffset(13, 45), $transition->offsetFrom);
        $this->assertEquals($this->createOffset(12, 45), $transition->offsetTo);

        /** @var \Spatie\IcalendarGenerator\Timezones\TimezoneTransition $first */
        $transition = $transitions[2];
        $this->assertEquals(new DateTime('2000-10-01T02:45:00+00:00'), $transition->start);
        $this->assertEquals(TimezoneEntryType::daylight(), $transition->type);
        $this->assertEquals($this->createOffset(12, 45), $transition->offsetFrom);
        $this->assertEquals($this->createOffset(13, 45), $transition->offsetTo);
    }

    /** @test */
    public function it_can_use_utc_as_timezone()
    {
        $resolver = new TimezoneTransitionsResolver(
            new DateTimeZone('UTC'),
            new DateTime('2000-01-01'),
            new DateTime()
        );

        $transitions = $resolver->getTransitions();

        /** @var \Spatie\IcalendarGenerator\Timezones\TimezoneTransition $first */
        $transition = $transitions[0];
        $this->assertEquals(new DateTime('1999-04-06T00:00:00+00:00'), $transition->start);
        $this->assertEquals(TimezoneEntryType::standard(), $transition->type);
        $this->assertEquals($this->createOffset(0, 0), $transition->offsetFrom);
        $this->assertEquals($this->createOffset(0, 0), $transition->offsetTo);
    }

    private function createOffset(int $hours, int $minutes = 0, bool $inverted = false)
    {
        $interval = new DateInterval('PT' . abs($hours) . 'H' . abs($minutes) . 'M');

        $interval->invert = $inverted;

        return $interval;
    }
}
