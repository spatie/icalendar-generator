<?php

namespace Spatie\IcalendarGenerator\ValueObjects;

use DateTimeInterface;
use Exception;
use Spatie\IcalendarGenerator\Enums\RecurrenceDay;
use Spatie\IcalendarGenerator\Enums\RecurrenceFrequency;
use Spatie\IcalendarGenerator\Enums\RecurrenceMonth;
use Spatie\IcalendarGenerator\Timezones\HasTimezones;
use Spatie\IcalendarGenerator\Timezones\TimezoneRangeCollection;

class RRule implements HasTimezones
{
    public static function frequency(RecurrenceFrequency $frequency): self
    {
        return new self($frequency);
    }

    /**
     * @param array<array{day: RecurrenceDay, index: ?int}> $weekdays
     * @param RecurrenceMonth[] $months
     * @param int[] $monthDays
     */
    public function __construct(
        public RecurrenceFrequency $frequency,
        public ?int $count = null,
        public ?int $interval = null,
        public ?DateTimeInterface $until = null,
        public ?DateTimeInterface $starting = null,
        public ?RecurrenceDay $weekStartsOn = null,
        public array $weekdays = [],
        public array $months = [],
        public array $monthDays = []
    ) {
    }

    public function interval(int $interval = 1): self
    {
        if ($interval < 1) {
            throw new Exception('Recurrence rule interval should be greater than 1');
        }

        $this->interval = $interval;

        return $this;
    }

    public function times(int $count): self
    {
        if ($count < 1) {
            throw new Exception('Recurrence rule count should be greater than 1');
        }

        $this->count = $count;

        return $this;
    }

    public function starting(DateTimeInterface $starting): self
    {
        $this->starting = $starting;

        return $this;
    }

    public function until(DateTimeInterface $until): self
    {
        $this->until = $until;

        return $this;
    }

    public function weekStartsOn(RecurrenceDay $weekStartsOn): self
    {
        $this->weekStartsOn = $weekStartsOn;

        return $this;
    }

    /**
     * @param int[]|int $monthDays
     */
    public function onMonthDay(array|int $monthDays): self
    {
        foreach (is_array($monthDays) ? $monthDays : [$monthDays] as $monthDay) {
            if (! in_array($monthDay, $this->monthDays)) {
                $this->monthDays[] = $monthDay;
            }
        }

        return $this;
    }

    /**
     * @param RecurrenceMonth[]|RecurrenceMonth|int|int[] $months
     */
    public function onMonth(RecurrenceMonth|int|array $months): self
    {
        foreach (is_array($months) ? $months : [$months] as $month) {
            if (is_int($month)) {
                $month = RecurrenceMonth::from($month);
            }

            if (! in_array($month, $this->months)) {
                $this->months[] = $month;
            }
        }

        return $this;
    }

    public function onWeekDay(RecurrenceDay $day, ?int $index = null): self
    {
        $value = [
            'day' => $day,
            'index' => $index,
        ];

        if (! in_array($value, $this->weekdays)) {
            $this->weekdays[] = $value;
        }

        return $this;
    }

    /** @return array<string, string|int> */
    public function compose(): array
    {
        $properties = ['FREQ' => $this->frequency->value];

        if ($this->starting !== null) {
            $properties['DTSTART'] = DateTimeValue::create($this->starting, true)->format();
        }

        if ($this->until !== null) {
            $properties['UNTIL'] = DateTimeValue::create($this->until, true)->format();
        }

        if ($this->count !== null) {
            $properties['COUNT'] = $this->count;
        }

        if ($this->interval !== null) {
            $properties['INTERVAL'] = $this->interval;
        }

        if ($this->weekStartsOn !== null) {
            $properties['WKST'] = $this->weekStartsOn->value;
        }

        if (count($this->weekdays) > 0) {
            $properties['BYDAY'] = implode(',', array_map(
                fn (array $day) => "{$day['index']}{$day['day']->value}",
                $this->weekdays
            ));
        }

        if (count($this->months) > 0) {
            $properties['BYMONTH'] = implode(',', array_map(
                fn (RecurrenceMonth $month) => $month->value,
                $this->months
            ));
        }

        if (count($this->monthDays) > 0) {
            $properties['BYMONTHDAY'] = implode(',', $this->monthDays);
        }

        return $properties;
    }

    public function getTimezoneRangeCollection(): TimezoneRangeCollection
    {
        return TimezoneRangeCollection::create()
            ->add($this->until)
            ->add($this->starting);
    }
}
