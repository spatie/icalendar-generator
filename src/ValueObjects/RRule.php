<?php

namespace Spatie\IcalendarGenerator\ValueObjects;

use Closure;
use DateTimeInterface;
use Exception;
use Spatie\IcalendarGenerator\Enums\RecurrenceDay;
use Spatie\IcalendarGenerator\Enums\RecurrenceFrequency;
use Spatie\IcalendarGenerator\Enums\RecurrenceMonth;
use Spatie\IcalendarGenerator\Timezones\HasTimezones;
use Spatie\IcalendarGenerator\Timezones\TimezoneRangeCollection;

class RRule implements HasTimezones
{
    public RecurrenceFrequency $frequency;

    public ?int $count = null;

    public ?int $interval = null;

    public ?DateTimeInterface $until = null;

    public ?DateTimeInterface $starting = null;

    private ?RecurrenceDay $weekStartsOn = null;

    /** @var array[] */
    public array $weekdays = [];

    /** @var \Spatie\IcalendarGenerator\Enums\RecurrenceMonth[] */
    public array $months = [];

    /** @var int[] */
    public array $monthDays = [];

    public static function frequency(RecurrenceFrequency $frequency): self
    {
        return new self($frequency);
    }

    public function __construct(RecurrenceFrequency $frequency)
    {
        $this->frequency = $frequency;
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
    public function onMonthDay($monthDays): self
    {
        $this->addAsCollection($this->monthDays, $monthDays, function ($value) {
            if (! is_int($value)) {
                throw new Exception('Month days should be int(s)');
            }
        });

        return $this;
    }

    /**
     * @param \Spatie\IcalendarGenerator\Enums\RecurrenceMonth[]|\Spatie\IcalendarGenerator\Enums\RecurrenceMonth|int|int[] $months
     */
    public function onMonth($months): self
    {
        $this->addAsCollection($this->months, $months, function ($value) {
            if (is_int($value)) {
                $value = RecurrenceMonth::make($value);
            }

            if (! $value instanceof RecurrenceMonth) {
                throw new Exception('Months should be int(s) or RecurrenceMonths');
            }

            return $value;
        });

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

    private function addAsCollection(array &$collection, $values, Closure $check)
    {
        $values = is_array($values) ? $values : [$values];

        foreach ($values as $value) {
            $value = $check($value) ?? $value;

            if (in_array($value, $collection)) {
                continue;
            }

            $collection[] = $value;
        }
    }
}
