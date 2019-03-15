<?php

namespace Spatie\Calendar\Components;

use DateTimeInterface;
use Spatie\Calendar\ComponentPayload;

class Alarm extends Component
{
    /** @var null|string */
    protected $description;

    /** @var string|DateTimeInterface */
    protected $trigger;

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

    public function triggerAtStartOfEvent(): Alarm
    {
        $this->trigger = 'START';

        return $this;
    }

    public function triggerAtEndOfEvent(): Alarm
    {
        $this->trigger = 'END';

        return $this;
    }

    public function triggerAt(DateTimeInterface $dateTime)
    {
        $this->trigger = $dateTime;

        return $this;
    }

    public function repeat()
    {

    }

    public function getPayload(): ComponentPayload
    {
        $payload =  ComponentPayload::new($this->getComponentType())
            ->textProperty('ACTION', 'DISPLAY')
            ->textProperty('DESCRIPTION', $this->description);

        if(is_string($this->trigger)){
            $payload->textProperty('TRIGGER', $this->trigger);
        }

        if($this->trigger instanceof DateTimeInterface){
            // TODO: should be something like this: TRIGGER;VALUE=DATE-TIME:19970317T133000Z
            $payload->dateTimeProperty('TRIGGER', $this->trigger);
        }

        return $payload;
    }
}
