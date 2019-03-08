<?php

namespace Spatie\Calendar\Components;

use Closure;
use Spatie\Calendar\ComponentPayload;

class Calendar extends Component
{
    /** @var array */
    protected $events = [];

    /** @var string */
    protected $name;

    public function getComponentType(): string
    {
        return 'CALENDAR';
    }

    public function getRequiredProperties(): array
    {
        return [
            'name'
        ];
    }

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function name(string $name): Calendar
    {
        return new self($name);
    }

    public function event($event): Calendar
    {
        if ($event instanceof Closure) {
            $event = $event(new Event());
        }

        $this->events[] = $event;

        return $this;
    }

    public function getPayload(): ComponentPayload
    {
        $this->ensureRequiredPropertiesAreSet();

        return ComponentPayload::new($this->getComponentType())
            ->textProperty('VERSION', '2.0')
            ->textProperty('PRODID', $this->name)
            ->subComponent(...$this->events);
    }
}
