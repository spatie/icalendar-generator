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
    private const TRIGGER_START = 'trigger_start';
    private const TRIGGER_END = 'trigger_end';
    private const TRIGGER_DATE = 'trigger_date';

    private DateTimeValue $triggerDate;

    private DateInterval $triggerInterval;

    private string $triggerMode = self::TRIGGER_DATE;

    private ?string $message;

    private bool $withoutTimezone = false;

    public static function date(DateTimeInterface $date, string $description = null): Alert
    {
        return static::create($description)->triggerDate($date);
    }

    public static function minutesBeforeStart(int $minutes, string $description = null): Alert
    {
        $interval = new DateInterval("PT{$minutes}M");
        $interval->invert = 1;

        return static::create($description)->triggerAtStart($interval);
    }

    public static function minutesAfterStart(int $minutes, string $description = null): Alert
    {
        return static::create($description)->triggerAtStart(new DateInterval("PT{$minutes}M"));
    }

    public static function minutesBeforeEnd(int $minutes, string $description = null): Alert
    {
        $interval = new DateInterval("PT{$minutes}M");
        $interval->invert = 1;

        return static::create($description)->triggerAtEnd($interval);
    }

    public static function minutesAfterEnd(int $minutes, string $description = null): Alert
    {
        return static::create($description)->triggerAtEnd(new DateInterval("PT{$minutes}M"));
    }

    private static function create(string $description = null): Alert
    {
        return new self($description);
    }

    public function __construct(string $description = null)
    {
        $this->message = $description;
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
        $this->withoutTimezone = true;

        return $this;
    }

    protected function payload(): ComponentPayload
    {
        return ComponentPayload::create($this->getComponentType())
            ->property(TextProperty::create('ACTION', 'DISPLAY'))
            ->optional($this->message, fn () => TextProperty::create('DESCRIPTION', $this->message))
            ->property($this->resolveTriggerProperty());
    }

    private function resolveTriggerProperty()
    {
        if ($this->triggerMode === self::TRIGGER_DATE) {
            return DateTimeProperty::create(
                'TRIGGER',
                $this->triggerDate,
                $this->withoutTimezone
            )->addParameter(new Parameter('VALUE', 'DATE-TIME'));
        }

        $property = DurationProperty::create('TRIGGER', $this->triggerInterval);

        if ($this->triggerMode === self::TRIGGER_END) {
            return $property->addParameter(new Parameter('RELATED', 'END'));
        }

        return $property;
    }
}
