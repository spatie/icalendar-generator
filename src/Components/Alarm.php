<?php

namespace Spatie\Calendar\Components;

use DateTimeInterface;
use function foo\func;
use Spatie\Calendar\ComponentPayload;
use Spatie\Calendar\Duration;
use Spatie\Calendar\PropertyTypes\DateTimePropertyType;
use Spatie\Calendar\PropertyTypes\Parameter;
use Spatie\Calendar\PropertyTypes\TextPropertyType;

final class Alarm extends Component
{
    /** @var null|string */
    private $description;

    /** @var \Spatie\Calendar\Duration|DateTimeInterface */
    private $trigger;

    /** @var int */
    private $repeatTimes;

    /** @var \Spatie\Calendar\Duration */
    private $repeatAfter;

    /** @var bool */
    private $triggerBeforeEvent = false;

    /** @var bool */
    private $triggerAfterEvent = false;

    public static function new(string $description = null): Alarm
    {
        return new self($description);
    }

    public function __construct(string $description = null)
    {
        $this->description = $description;
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
        return ComponentPayload::new($this->getComponentType())
            ->textProperty('ACTION', 'DISPLAY')
            ->textProperty('DESCRIPTION', $this->description)
            ->when($this->trigger instanceof DateTimeInterface, function (ComponentPayload $payload) {
                $payload->property(
                    new DateTimePropertyType('TRIGGER', $this->trigger),
                    [new Parameter('VALUE', 'DATE-TIME')]
                );
            })
            ->when($this->trigger instanceof Duration, function (ComponentPayload $payload) {
                $triggerProperty = new TextPropertyType('TRIGGER', $this->trigger->build());

                if ($this->triggerBeforeEvent) {
                    $triggerProperty->addParameter(new Parameter('RELATED', 'START'));
                }

                if ($this->triggerAfterEvent) {
                    $triggerProperty->addParameter(new Parameter('RELATED', 'END'));
                }

                $payload->property($triggerProperty);
            })
            ->when($this->repeatAfter && $this->repeatTimes, function (ComponentPayload $payload) {
                $payload->textProperty('DURATION', $this->repeatAfter->build());
                $payload->textProperty('REPEAT', $this->repeatTimes);
            });
    }
}
