<?php

namespace Spatie\Calendar\Components;

use Closure;
use Spatie\Calendar\ComponentPayload;

class Calendar extends Component
{
    /** @var array */
    protected $events = [];

    /** @var string|null */
    protected $name;

    /** @var string|null */
    protected $description;

    public function getComponentType(): string
    {
        return 'CALENDAR';
    }

    public function getRequiredProperties(): array
    {
        return [
            'VERSION',
            'PRODID',
        ];
    }

    public static function new(): Calendar
    {
        return new self();
    }

    public function name(string $name): Calendar
    {
        $this->name = $name;

        return $this;
    }

    public function description(string $description): Calendar
    {
        $this->description = $description;

        return $this;
    }

    public function event($event): Calendar
    {
        if (is_array($event)) {
            foreach ($event as $item) {
                $this->addEvent($item);
            }
        } else {
            $this->addEvent($event);
        }

        return $this;
    }

    public function get(): string
    {
        return $this->toString();
    }

    public function getPayload(): ComponentPayload
    {
        return ComponentPayload::new($this->getComponentType())
            ->textProperty('VERSION', '2.0')
            ->textProperty('PRODID', 'Spatie/iCalendar-generator')
            ->textProperty('NAME', $this->name)
            ->textProperty('DESCRIPTION', $this->description)
            ->subComponent(...$this->events);
    }

    protected function addEvent($event)
    {
        if ($event instanceof Closure) {
            $injectedEvent = new Event();

            $event($injectedEvent);

            $event = $injectedEvent;
        }

        $this->events[] = $event;

        return $this;
    }
}
