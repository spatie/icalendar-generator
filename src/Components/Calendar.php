<?php

namespace Spatie\Calendar\Components;

use Spatie\Calendar\ComponentPayload;
use Spatie\Calendar\Duration;
use Spatie\Calendar\HasSubComponents;
use Spatie\Calendar\PropertyTypes\Parameter;
use Spatie\Calendar\PropertyTypes\TextPropertyType;

final class Calendar extends Component
{
    use HasSubComponents;

    /** @var array */
    private $events = [];

    /** @var string|null */
    private $name;

    /** @var string|null */
    private $description;

    /** @var bool */
    private $withTimezone = false;

    /** @var \Spatie\Calendar\Duration|null */
    private $refreshInterval;

    public static function create(string $name = null): Calendar
    {
        return new self($name);
    }

    public function __construct(string $name = null)
    {
        $this->name = $name;
    }

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

    public function withTimezone(): Calendar
    {
        $this->withTimezone = true;

        return $this;
    }

    public function refreshInterval(Duration $duration): Calendar
    {
        $this->refreshInterval = $duration;

        return $this;
    }

    public function get(): string
    {
        return $this->toString();
    }

    public function getPayload(): ComponentPayload
    {
        $subComponents = $this->subComponents;

        if ($this->withTimezone) {
            array_walk($subComponents, function (Component $subComponent) {
                return $subComponent instanceof Event
                    ? $subComponent->withTimezone()
                    : $subComponent;
            });
        }

        return ComponentPayload::create($this->getComponentType())
            ->textProperty('VERSION', '2.0')
            ->textProperty('PRODID', 'Spatie/iCalendar-generator')
            ->textProperty('NAME', $this->name)
            ->alias('NAME', ['X-WR-CALNAME'])
            ->textProperty('DESCRIPTION', $this->description)
            ->when(! is_null($this->refreshInterval), function (ComponentPayload $payload) {
                $payload->property(
                    new TextPropertyType('REFRESH-INTERVAL', $this->refreshInterval->build()),
                    [new Parameter('VALUE', 'DURATION')]
                );
            })
            ->subComponent(...$subComponents);
    }
}
