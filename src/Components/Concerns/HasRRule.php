<?php

namespace Spatie\IcalendarGenerator\Components\Concerns;

use DateTimeInterface;
use Spatie\IcalendarGenerator\ComponentPayload;
use Spatie\IcalendarGenerator\Properties\DateTimeProperty;
use Spatie\IcalendarGenerator\Properties\Parameter;
use Spatie\IcalendarGenerator\Properties\RRuleProperty;
use Spatie\IcalendarGenerator\Properties\TextProperty;
use Spatie\IcalendarGenerator\ValueObjects\DateTimeValue;
use Spatie\IcalendarGenerator\ValueObjects\RRule;

trait HasRRule
{
    /** @var RRule|string|null */
    protected $rrule = null;

    protected ?DateTimeInterface $rruleStarting = null;

    protected ?DateTimeInterface $rruleUntil = null;

    /** @var DateTimeValue[] */
    protected array $recurrenceDates = [];

    /** @var DateTimeValue[] */
    public array $excludedRecurrenceDates = [];

    public function rrule(RRule $rrule): self
    {
        $this->rrule = $rrule;

        return $this;
    }

    public function rruleAsString(string $rrule, ?DateTimeInterface $starting = null, ?DateTimeInterface $until = null): self
    {
        $this->rrule = $rrule;
        $this->rruleStarting = $starting;
        $this->rruleUntil = $until;

        return $this;
    }

    /**
     * @param DateTimeInterface[]|DateTimeInterface $dates
     */
    public function doNotRepeatOn(array|DateTimeInterface $dates, bool $withTime = true): self
    {
        $dates = array_map(
            fn (DateTimeInterface $date) => DateTimeValue::create($date, $withTime),
            is_array($dates) ? $dates : [$dates]
        );

        $this->excludedRecurrenceDates = array_merge($this->excludedRecurrenceDates, $dates);

        return $this;
    }

    /**
     * @param DateTimeInterface[]|DateTimeInterface $dates
     */
    public function repeatOn(array|DateTimeInterface $dates, bool $withTime = true): self
    {
        $dates = array_map(
            fn (DateTimeInterface $date) => DateTimeValue::create($date, $withTime),
            is_array($dates) ? $dates : [$dates]
        );

        $this->recurrenceDates = array_merge($this->recurrenceDates, $dates);

        return $this;
    }

    protected function resolveRruleProperties(ComponentPayload $payload): self
    {
        if ($this->rrule) {
            $property = is_string($this->rrule)
                ? TextProperty::create('RRULE', $this->rrule)->withoutEscaping()
                : RRuleProperty::create('RRULE', $this->rrule);

            $payload->property($property);
        }


        foreach ($this->recurrenceDates as $recurrenceDate) {
            $payload->property(self::dateTimePropertyWithSpecifiedType('RDATE', $recurrenceDate));
        }

        foreach ($this->excludedRecurrenceDates as $excludedRecurrenceDate) {
            $payload->property(self::dateTimePropertyWithSpecifiedType('EXDATE', $excludedRecurrenceDate));
        }

        return $this;
    }

    /**
     * @return array{0: array{0: DateTimeInterface|null, 1: DateTimeInterface|null}|RRule|string|null, 1: DateTimeValue[], 2: DateTimeValue[]}
     */
    protected function getRRuleTimezoneEntries(): array
    {
        return [
            is_string($this->rrule) ? [$this->rruleStarting, $this->rruleUntil] : $this->rrule,
            $this->recurrenceDates,
            $this->excludedRecurrenceDates,
        ];
    }

    protected static function dateTimePropertyWithSpecifiedType(
        string $name,
        DateTimeValue $dateTimeValue
    ): DateTimeProperty {
        $property = DateTimeProperty::create($name, $dateTimeValue);

        if ($dateTimeValue->hasTime()) {
            $property->addParameter(Parameter::create('VALUE', 'DATE-TIME'));
        }

        return $property;
    }
}
