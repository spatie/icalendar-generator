<?php

namespace Spatie\IcalendarGenerator\ValueObjects;

use DateTimeInterface;
use Exception;
use Spatie\IcalendarGenerator\Timezones\HasTimezones;
use Spatie\IcalendarGenerator\Timezones\TimezoneRangeCollection;

class PeriodValue implements HasTimezones
{
    private DateTimeInterface $starting;

    private ?DateTimeInterface $ending = null;

    private ?DurationValue $duration = null;

    private function __construct(
        DateTimeInterface $starting,
        ?DateTimeInterface $ending,
        ?DurationValue $duration
    ) {
        $this->starting = $starting;
        $this->ending = $ending;
        $this->duration = $duration;
    }

    /**
     * @param \DateTimeInterface $starting
     * @param DateTimeInterface|\Spatie\IcalendarGenerator\ValueObjects\DurationValue $ending
     *
     * @return static
     */
    public static function create(DateTimeInterface $starting, $ending): self
    {
        if ($ending instanceof DateTimeInterface) {
            /** @psalm-suppress InvalidArgument */
            return new self($starting, $ending, null);
        }

        if ($ending instanceof DurationValue) {
            return new self($starting, null, $ending);
        }

        throw new Exception('The end of a period can only be a DateTime or Duration');
    }

    public function format(): string
    {
        if ($this->duration !== null) {
            return DateTimeValue::create($this->starting, true)->format() . '/' . $this->duration->format();
        }

        return DateTimeValue::create($this->starting, true)->format() . '/' . DateTimeValue::create($this->ending, true)->format();
    }

    public function getTimezoneRangeCollection(): TimezoneRangeCollection
    {
        return TimezoneRangeCollection::create()
            ->add($this->starting)
            ->add($this->ending);
    }
}
