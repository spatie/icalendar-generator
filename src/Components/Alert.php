<?php

namespace Spatie\IcalendarGenerator\Components;

use DateInterval;
use DateTimeInterface;
use Spatie\IcalendarGenerator\ComponentPayload;
use Spatie\IcalendarGenerator\Properties\DateTimeProperty;
use Spatie\IcalendarGenerator\Properties\DurationProperty;
use Spatie\IcalendarGenerator\Properties\Parameter;
use Spatie\IcalendarGenerator\Properties\TextProperty;
use Spatie\IcalendarGenerator\ValueObjects\DateTimeValue;

class Alert extends Component
{
    protected const TRIGGER_START = 'trigger_start';
    protected const TRIGGER_END = 'trigger_end';
    protected const TRIGGER_DATE = 'trigger_date';

    protected DateTimeValue $triggerDate;

    protected DateInterval $triggerInterval;

    protected string $triggerMode = self::TRIGGER_DATE;

    protected bool $withTimezone = true;

    public static function date(DateTimeInterface $date, ?string $description = null): self
    {
        return self::create($description)->triggerDate($date);
    }

    public static function minutesBeforeStart(int $minutes, ?string $description = null): self
    {
        $interval = new DateInterval("PT{$minutes}M");
        $interval->invert = 1;

        return self::create($description)->triggerAtStart($interval);
    }

    public static function minutesAfterStart(int $minutes, ?string $description = null): self
    {
        return self::create($description)->triggerAtStart(new DateInterval("PT{$minutes}M"));
    }

    public static function minutesBeforeEnd(int $minutes, ?string $description = null): self
    {
        $interval = new DateInterval("PT{$minutes}M");
        $interval->invert = 1;

        return self::create($description)->triggerAtEnd($interval);
    }

    public static function minutesAfterEnd(int $minutes, ?string $description = null): self
    {
        return self::create($description)->triggerAtEnd(new DateInterval("PT{$minutes}M"));
    }

    protected static function create(?string $description = null): self
    {
        return new self($description);
    }

    public function __construct(protected ?string $message = null)
    {
    }

    public function getComponentType(): string
    {
        return 'VALARM';
    }

    public function getRequiredProperties(): array
    {
        return [
            'ACTION',
            'TRIGGER',
            'DESCRIPTION',
        ];
    }

    public function message(string $message): Alert
    {
        $this->message = $message;

        return $this;
    }

    public function triggerDate(DateTimeInterface $triggerAt): Alert
    {
        $this->triggerMode = self::TRIGGER_DATE;
        $this->triggerDate = DateTimeValue::create($triggerAt, true);

        return $this;
    }

    public function triggerAtStart(DateInterval $interval): Alert
    {
        $this->triggerMode = self::TRIGGER_START;
        $this->triggerInterval = $interval;

        return $this;
    }

    public function triggerAtEnd(DateInterval $interval): Alert
    {
        $this->triggerMode = self::TRIGGER_END;
        $this->triggerInterval = $interval;

        return $this;
    }

    public function withoutTimezone(): Alert
    {
        $this->withTimezone = false;

        return $this;
    }

    protected function payload(): ComponentPayload
    {
        $payload = ComponentPayload::create($this->getComponentType())
            ->property(TextProperty::create('ACTION', 'DISPLAY'))
            ->property($this->resolveTriggerProperty());

        if ($this->message) {
            $payload->property(TextProperty::create('DESCRIPTION', $this->message));
        }

        return $payload;
    }

    protected function resolveTriggerProperty(): DateTimeProperty|DurationProperty
    {
        if ($this->triggerMode === self::TRIGGER_DATE) {
            $date = $this->withTimezone
                ? $this->triggerDate
                : (clone $this->triggerDate)->disableTimezone();

            return DateTimeProperty::create(
                'TRIGGER',
                $date,
            )->addParameter(new Parameter('VALUE', 'DATE-TIME'));
        }

        $property = DurationProperty::create('TRIGGER', $this->triggerInterval);

        if ($this->triggerMode === self::TRIGGER_END) {
            return $property->addParameter(new Parameter('RELATED', 'END'));
        }

        return $property;
    }
}
