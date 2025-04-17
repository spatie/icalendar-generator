<?php

namespace Spatie\IcalendarGenerator\Components;

use DateTimeInterface;
use Spatie\IcalendarGenerator\ComponentPayload;
use Spatie\IcalendarGenerator\Enums\TimezoneEntryType;
use Spatie\IcalendarGenerator\Properties\DateTimeProperty;
use Spatie\IcalendarGenerator\Properties\RRuleProperty;
use Spatie\IcalendarGenerator\Properties\TextProperty;
use Spatie\IcalendarGenerator\Timezones\TimezoneTransition;
use Spatie\IcalendarGenerator\ValueObjects\DateTimeValue;
use Spatie\IcalendarGenerator\ValueObjects\RRule;

class TimezoneEntry extends Component
{
    protected DateTimeValue $starts;

    public static function create(
        TimezoneEntryType $type,
        DateTimeInterface $starts,
        string $offsetFrom,
        string $offsetTo
    ): self {
        return new self($type, $starts, $offsetFrom, $offsetTo);
    }

    public static function createFromTransition(TimezoneTransition $transition): self
    {
        return new self(
            $transition->type,
            $transition->start,
            $transition->offsetFrom->format('%R%H%M'),
            $transition->offsetTo->format('%R%H%M')
        );
    }

    public function __construct(
        protected TimezoneEntryType $type,
        DateTimeInterface $starts,
        protected string $offsetFrom,
        protected string $offsetTo,
        protected ?string $name = null,
        protected ?string $description = null,
        protected ?RRule $rrule = null,
    ) {
        $this->starts = DateTimeValue::create($starts);
    }

    public function name(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function description(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function rrule(RRule $rrule): self
    {
        $this->rrule = $rrule;

        return $this;
    }

    public function getComponentType(): string
    {
        return (string) $this->type->value;
    }

    /** @return array<string> */
    public function getRequiredProperties(): array
    {
        return [
            'DTSTART',
            'TZOFFSETFROM',
            'TZOFFSETTO',
        ];
    }

    protected function payload(): ComponentPayload
    {
        $payload = ComponentPayload::create($this->getComponentType())
            ->property(DateTimeProperty::create('DTSTART', (clone $this->starts)->disableTimezone()))
            ->property(TextProperty::create('TZOFFSETFROM', $this->offsetFrom))
            ->property(TextProperty::create('TZOFFSETTO', $this->offsetTo));

        if ($this->name) {
            $payload->property(TextProperty::create('TZNAME', $this->name));
        }

        if ($this->description) {
            $payload->property(TextProperty::create('COMMENT', $this->description));
        }

        if ($this->rrule) {
            $payload->property(RRuleProperty::create('RRULE', $this->rrule));
        }

        return $payload;
    }
}
