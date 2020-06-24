<?php

namespace Spatie\IcalendarGenerator\Properties;

use Spatie\IcalendarGenerator\ValueObjects\RecurrenceRule;

class RecurrenceRuleProperty extends Property
{
    private RecurrenceRule $recurrenceRule;

    public static function create(string $name, RecurrenceRule $recurrenceRule): self
    {
        return new self($name, $recurrenceRule);
    }

    public function __construct(string $name, RecurrenceRule $recurrenceRule)
    {
        $this->name = $name;
        $this->recurrenceRule = $recurrenceRule;
    }

    public function getValue(): string
    {
        $segments = [];

        foreach ($this->recurrenceRule->compose() as $property => $value) {
            $segments[] = "{$property}={$value}";
        }

        return "RRULE:" . implode(';', $segments);
    }

    public function getOriginalValue(): RecurrenceRule
    {
        return$this->recurrenceRule;
    }
}
