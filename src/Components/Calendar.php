<?php

namespace Spatie\IcalendarGenerator\Components;

use Closure;
use Spatie\IcalendarGenerator\ComponentPayload;
use Spatie\IcalendarGenerator\PropertyTypes\DurationPropertyType;
use Spatie\IcalendarGenerator\PropertyTypes\Parameter;

final class Calendar extends Component
{
    /** @var array */
    private $events = [];

    /** @var string|null */
    private $name;

    /** @var string|null */
    private $description;

    /** @var bool */
    private $withTimezone = false;

    /** @var int|null */
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

    /**
     * @param $event \Spatie\IcalendarGenerator\Components\Event|array|Closure
     *
     * @return \Spatie\IcalendarGenerator\Components\Calendar
     */
    public function event($event): Calendar
    {
        if (is_null($event)) {
            return $this;
        }

        $events = is_array($event) ? $event : [$event];

        $this->events = array_map(function ($eventToResolve) {
            if (! is_callable($eventToResolve)) {
                return $eventToResolve;
            }

            $newEvent = new Event();

            $eventToResolve($newEvent);

            return $newEvent;
        }, $events);

        return $this;
    }

    public function withTimezone(): Calendar
    {
        $this->withTimezone = true;

        return $this;
    }

    public function refreshInterval(int $minutes): Calendar
    {
        $this->refreshInterval = $minutes;

        return $this;
    }

    public function get(): string
    {
        return $this->toString();
    }

    public function getPayload(): ComponentPayload
    {
        $events = $this->events;

        if ($this->withTimezone) {
            array_walk($events, function (Event $event) {
                $event->withTimezone();
            });
        }

        return ComponentPayload::create($this->getComponentType())
            ->textProperty('VERSION', '2.0')
            ->textProperty('PRODID', 'spatie/icalendar-generator')
            ->textProperty(['NAME', 'X-WR-CALNAME'], $this->name)
            ->textProperty(['DESCRIPTION', 'X-WR-CALDESC'], $this->description)
            ->when(! is_null($this->refreshInterval), function (ComponentPayload $payload) {
                $payload
                    ->property(
                        DurationPropertyType::create('REFRESH-INTERVAL', $this->refreshInterval)
                            ->addParameter(new Parameter('VALUE', 'DURATION'))
                    )
                    ->property(
                        DurationPropertyType::create('X-PUBLISHED-TTL', $this->refreshInterval)
                    );
            })
            ->subComponent(...$events);
    }
}
