<?php

namespace Spatie\IcalendarGenerator\Properties;

use Spatie\IcalendarGenerator\ValueObjects\RRule;

class RRuleProperty extends Property
{
    private RRule $recurrenceRule;

    public static function create(string $name, RRule $recurrenceRule): self
    {
        return new self($name, $recurrenceRule);
    }

    public function __construct(string $name, RRule $recurrenceRule)
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

        return implode(';', $segments);
    }

    public function getOriginalValue(): RRule
    {
        return$this->recurrenceRule;
    }
}
