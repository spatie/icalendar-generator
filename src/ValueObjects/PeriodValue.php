<?php

namespace Spatie\IcalendarGenerator\ValueObjects;

use DateTimeInterface;
use Exception;
use Spatie\IcalendarGenerator\Timezones\HasTimezones;
use Spatie\IcalendarGenerator\Timezones\TimezoneRangeCollection;

class PeriodValue implements HasTimezones
{
    protected function __construct(
        protected DateTimeInterface $starting,
        protected ?DateTimeInterface $ending,
        protected ?DurationValue $duration
    ) {
    }

    public static function create(DateTimeInterface $starting, DateTimeInterface|DurationValue $ending): self
    {
        if ($ending instanceof DateTimeInterface) {
            /** @psalm-suppress InvalidArgument */
            return new self($starting, $ending, null);
        }

        return new self($starting, null, $ending);
    }

    public function format(): string
    {
        if ($this->duration !== null) {
            return DateTimeValue::create($this->starting)->format().'/'.$this->duration->format();
        }

        if ($this->ending !== null) {
            return DateTimeValue::create($this->starting)->format().'/'.DateTimeValue::create($this->ending)->format();

        }

        throw new Exception('A period should have an end or duration');
    }

    public function getTimezoneRangeCollection(): TimezoneRangeCollection
    {
        return TimezoneRangeCollection::create()
            ->add($this->starting)
            ->add($this->ending);
    }
}
