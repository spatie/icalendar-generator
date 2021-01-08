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
    private TimezoneEntryType $type;

    private DateTimeValue $starts;

    private string $offsetFrom;

    private string $offsetTo;

    private ?RRule $rrule = null;

    private ?string $name = null;

    private ?string $description = null;

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
        TimezoneEntryType $type,
        DateTimeInterface $starts,
        string $offsetFrom,
        string $offsetTo
    ) {
        $this->type = $type;
        $this->starts = DateTimeValue::create($starts);
        $this->offsetFrom = $offsetFrom;
        $this->offsetTo = $offsetTo;
    }

    public function name(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function description(string $description)
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
        return ComponentPayload::create($this->getComponentType())
            ->property(DateTimeProperty::create('DTSTART', $this->starts, true))
            ->property(TextProperty::create('TZOFFSETFROM', $this->offsetFrom))
            ->property(TextProperty::create('TZOFFSETTO', $this->offsetTo))
            ->optional(
                $this->name,
                fn () => TextProperty::create('TZNAME', $this->name)
            )
            ->optional(
                $this->description,
                fn () => TextProperty::create('COMMENT', $this->description)
            )
            ->optional(
                $this->rrule,
                fn () => RRuleProperty::create('RRULE', $this->rrule)
            );
    }
}
