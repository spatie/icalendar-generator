<?php

namespace Spatie\Calendar\Components;

use Spatie\Calendar\ComponentPayload;

class Calendar implements Component
{
    /** @var array  */
    protected $events = [];

    /** @var string */
    protected $name;

    public static function create(): Calendar
    {
        return new self();
    }

    public function name(string $name) : Calendar
    {
        $this->name = $name;

        return $this;
    }

    public function addEvent(Event $event): Calendar
    {
        $this->events[] = $event;

        return $this;
    }

    public function getPayload(): ComponentPayload
    {
        return ComponentPayload::new('CALENDAR')
            ->add('VERSION', '2.0')
            ->add('PRODID', $this->name)
            ->addComponents($this->events);
    }
}
