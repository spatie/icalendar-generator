<?php

namespace Spatie\Calendar\Components;

use Spatie\Calendar\ComponentPayload;
use Spatie\Calendar\HasSubComponents;

class Calendar extends Component
{
    use HasSubComponents;

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
        $this->addSubComponent($event);

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
            ->subComponent(...$this->subComponents);
    }
}
