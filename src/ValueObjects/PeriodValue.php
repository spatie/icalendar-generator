<?php

namespace Spatie\IcalendarGenerator\ValueObjects;

use DateTimeInterface;
use Exception;

class PeriodValue
{
    private DateTimeInterface $staring;

    private ?DateTimeInterface $ending = null;

    private ?DurationValue $duration = null;

    private function __construct(
        DateTimeInterface $staring,
        ?DateTimeInterface $ending,
        ?DurationValue $duration
    ) {
        $this->staring = $staring;
        $this->ending = $ending;
        $this->duration = $duration;
    }

    /**
     * @param \DateTimeInterface $staring
     * @param DateTimeInterface|\Spatie\IcalendarGenerator\ValueObjects\DurationValue $ending
     *
     * @return static
     */
    public static function create(DateTimeInterface $staring, $ending): self
    {
        if ($ending instanceof DateTimeInterface) {
            return new self($staring, $ending, null);
        }

        if ($ending instanceof DurationValue) {
            return new self($staring, null, $ending);
        }

        throw new Exception('The end of a period can only be a DateTime or Duration');
    }

    public function format(): string
    {
        if ($this->duration !== null) {
            return DateTimeValue::create($this->staring, true)->format() . '/' . $this->duration->format();
        }

        return DateTimeValue::create($this->staring, true)->format() . '/' . DateTimeValue::create($this->ending, true)->format();
    }
}
