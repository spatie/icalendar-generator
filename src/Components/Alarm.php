<?php

namespace Spatie\Calendar\Components;

use DateTimeInterface;
use Spatie\Calendar\ComponentPayload;
use Spatie\Calendar\Duration;
use Spatie\Calendar\PropertyTypes\DateTimePropertyType;
use Spatie\Calendar\PropertyTypes\Parameter;
use Spatie\Calendar\PropertyTypes\TextPropertyType;

class Alarm extends Component
{
    /** @var null|string */
    protected $description;

    /** @var Duration|DateTimeInterface */
    protected $trigger;

    /** @var int */
    protected $repeatTimes;

    /** @var Duration */
    protected $repeatAfter;

    /** @var bool */
    protected $triggerBeforeEvent = false;

    /** @var bool */
    protected $triggerAfterEvent = false;

    public function __construct(?string $description = null)
    {
        $this->description = $description;
    }

    public static function new(?string $description = null): Alarm
    {
        return new self($description);
    }

    public function getComponentType(): string
    {
        return 'ALARM';
    }

    public function getRequiredProperties(): array
    {
        return [
            'ACTION',
            'TRIGGER',
            'DESCRIPTION',
        ];
    }

    public function description(string $description): Alarm
    {
        $this->description = $description;

        return $this;
    }

    public function triggerBeforeEvent(Duration $duration): Alarm
    {
        $this->trigger = $duration;
        $this->triggerBeforeEvent = true;

        return $this;
    }

    public function triggerAfterEvent(Duration $duration): Alarm
    {
        $this->trigger = $duration;
        $this->triggerAfterEvent = true;

        return $this;
    }

    public function triggerAt(DateTimeInterface $triggerAt): Alarm
    {
        $this->trigger = $triggerAt;

        return $this;
    }

    public function repeat(Duration $after, int $times = 1): Alarm
    {
        $this->repeatAfter = $after;
        $this->repeatTimes = $times;

        return $this;
    }

    public function getPayload(): ComponentPayload
    {
        $payload = ComponentPayload::new($this->getComponentType())
            ->textProperty('ACTION', 'DISPLAY')
            ->textProperty('DESCRIPTION', $this->description);

        if ($this->trigger instanceof DateTimeInterface) {
            $payload->property(
                (new DateTimePropertyType('TRIGGER', $this->trigger))
                    ->addParameter(new Parameter('VALUE', 'DATE-TIME'))
            );
        }

        if ($this->trigger instanceof Duration) {
            $triggerProperty = new TextPropertyType('TRIGGER', $this->trigger->build());

            if ($this->triggerBeforeEvent) {
                $triggerProperty->addParameter(new Parameter('RELATED', 'START'));
            }

            if ($this->triggerAfterEvent) {
                $triggerProperty->addParameter(new Parameter('RELATED', 'END'));
            }

            $payload->property($triggerProperty);
        }

        if ($this->repeatAfter && $this->repeatTimes) {
            $payload->textProperty('DURATION', $this->repeatAfter->build());
            $payload->textProperty('REPEAT', $this->repeatTimes);
        }

        return $payload;
    }
}
