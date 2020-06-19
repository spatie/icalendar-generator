<?php

namespace Spatie\IcalendarGenerator\Tests\ValueObjects;

use DateTime;
use Exception;
use PHPUnit\Framework\TestCase;
use Spatie\IcalendarGenerator\Enums\RecurrenceDay;
use Spatie\IcalendarGenerator\Enums\RecurrenceFrequency;
use Spatie\IcalendarGenerator\Enums\RecurrenceMonth;
use Spatie\IcalendarGenerator\ValueObjects\RecurrenceRule;


class RecurrenceRuleTest extends TestCase
{
    /** @test */
    public function it_can_create_an_rrule()
    {
        $rrule = RecurrenceRule::frequency(RecurrenceFrequency::daily())->compose();

        $this->assertEquals([
            "FREQ" => "DAILY",
        ], $rrule);
    }

    /** @test */
    public function it_can_set_the_start_date()
    {
        $rrule = RecurrenceRule::frequency(RecurrenceFrequency::daily())
            ->starting(new DateTime('16 may 1994'))
            ->compose();

        $this->assertEquals([
            "FREQ" => "DAILY",
            'DTSTART' => '19940516T000000',
        ], $rrule);
    }

    /** @test */
    public function it_can_set_until()
    {
        $rrule = RecurrenceRule::frequency(RecurrenceFrequency::daily())
            ->until(new DateTime('16 may 1994'))
            ->compose();

        $this->assertEquals([
            "FREQ" => "DAILY",
            'UNTIL' => '19940516T000000',
        ], $rrule);
    }

    /** @test */
    public function it_can_set_count()
    {
        $rrule = RecurrenceRule::frequency(RecurrenceFrequency::daily())
            ->times(10)
            ->compose();

        $this->assertEquals([
            "FREQ" => "DAILY",
            'COUNT' => '10',
        ], $rrule);
    }

    /** @test */
    public function it_cannot_set_a_negative_count()
    {
        $this->expectException(Exception::class);

        RecurrenceRule::frequency(RecurrenceFrequency::daily())
            ->times(-1)
            ->compose();
    }

    /** @test */
    public function it_can_set_interval()
    {
        $rrule = RecurrenceRule::frequency(RecurrenceFrequency::daily())
            ->every(10)
            ->compose();

        $this->assertEquals([
            "FREQ" => "DAILY",
            'INTERVAL' => '10',
        ], $rrule);
    }

    /** @test */
    public function it_cannot_set_a_negative_interval()
    {
        $this->expectException(Exception::class);

        RecurrenceRule::frequency(RecurrenceFrequency::daily())
            ->every(-1)
            ->compose();
    }

    /** @test */
    public function it_can_set_the_week_starts_on()
    {
        $rrule = RecurrenceRule::frequency(RecurrenceFrequency::daily())
            ->weekStartsOn(RecurrenceDay::monday())
            ->compose();

        $this->assertEquals([
            "FREQ" => "DAILY",
            'WKST' => 'MO',
        ], $rrule);
    }

    /**
     * @test
     * @dataProvider weekDaysProvider
     *
     * @param array $days
     * @param string $expected
     */
    public function it_can_add_weekdays(array $days, string $expected)
    {
        $rrule = RecurrenceRule::frequency(RecurrenceFrequency::daily());

        foreach ($days as $day) {
            $rrule->onWeekDay($day['day'], $day['index']);
        }

        $this->assertEquals([
            "FREQ" => "DAILY",
            'BYDAY' => $expected,
        ], $rrule->compose());
    }

    public function weekDaysProvider(): array
    {
        return [
            [
                'days' => [
                    ['day' => RecurrenceDay::monday(), 'index' => null],
                ],
                'expected' => 'MO',
            ],
            [
                'days' => [
                    ['day' => RecurrenceDay::monday(), 'index' => 2],
                ],
                'expected' => '2MO',
            ],
            [
                'days' => [
                    ['day' => RecurrenceDay::monday(), 'index' => -1],
                ],
                'expected' => '-1MO',
            ],
            [
                'days' => [
                    ['day' => RecurrenceDay::monday(), 'index' => null],
                    ['day' => RecurrenceDay::monday(), 'index' => 2],
                    ['day' => RecurrenceDay::monday(), 'index' => -1],
                ],
                'expected' => 'MO,2MO,-1MO',
            ],
            [
                'days' => [
                    ['day' => RecurrenceDay::monday(), 'index' => null],
                    ['day' => RecurrenceDay::monday(), 'index' => null],
                    ['day' => RecurrenceDay::monday(), 'index' => -1],
                    ['day' => RecurrenceDay::monday(), 'index' => -1],
                ],
                'expected' => 'MO,-1MO',
            ],
        ];
    }

    /** @test
     * @dataProvider monthsProvider
     *
     * @param RecurrenceMonth[]|RecurrenceMonth $months
     * @param string $expected
     */
    public function it_can_add_months($months, string $expected)
    {
        $rrule = RecurrenceRule::frequency(RecurrenceFrequency::daily())
            ->onMonth($months)
            ->compose();

        $this->assertEquals([
            "FREQ" => "DAILY",
            'BYMONTH' => $expected,
        ], $rrule);
    }

    public function monthsProvider(): array
    {
        return [
            [
                'months' => RecurrenceMonth::may(),
                'expected' => '5',
            ],
            [
                'months' => 5,
                'expected' => '5',
            ],
            [
                'months' => [RecurrenceMonth::april(), RecurrenceMonth::may()],
                'expected' => '4,5',
            ],
            [
                'months' => [4, 5],
                'expected' => '4,5',
            ],
            [
                'months' => [RecurrenceMonth::may(), 5],
                'expected' => '5',
            ],
        ];
    }

    /** @test
     * @dataProvider monthsDaysProvider
     *
     * @param int|int[] $monthDays
     * @param string $expected
     */
    public function it_can_add_month_days($monthDays, string $expected)
    {
        $rrule = RecurrenceRule::frequency(RecurrenceFrequency::daily())
            ->onMonthDay($monthDays)
            ->compose();

        $this->assertEquals([
            "FREQ" => "DAILY",
            'BYMONTHDAY' => $expected,
        ], $rrule);
    }

    public function monthsDaysProvider(): array
    {
        return [
            [
                'monthsDays' => 1,
                'expected' => '1',
            ],
            [
                'monthsDays' => [1,2,3],
                'expected' => '1,2,3',
            ],
            [
                'monthsDays' => [1,2,1],
                'expected' => '1,2',
            ],
        ];
    }
}
