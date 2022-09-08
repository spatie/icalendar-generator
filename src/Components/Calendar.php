<?php

namespace Spatie\IcalendarGenerator\Components;

use Closure;
use DateInterval;
use DateTimeZone;
use Spatie\IcalendarGenerator\ComponentPayload;
use Spatie\IcalendarGenerator\Properties\DurationProperty;
use Spatie\IcalendarGenerator\Properties\Parameter;
use Spatie\IcalendarGenerator\Properties\TextProperty;
use Spatie\IcalendarGenerator\Timezones\HasTimezones;
use Spatie\IcalendarGenerator\Timezones\TimezoneRangeCollection;
use Spatie\IcalendarGenerator\Timezones\TimezoneTransition;
use Spatie\IcalendarGenerator\Timezones\TimezoneTransitionsResolver;

class Calendar extends Component implements HasTimezones
{
    /** @var \Spatie\IcalendarGenerator\Components\Event[] */
    private array $events = [];

    /** @var \Spatie\IcalendarGenerator\Components\Timezone[] */
    private array $timezones = [];

    private ?string $name = null;

    private ?string $description = null;

    private bool $withoutTimezone = false;

    private bool $withoutAutoTimezoneComponents = false;

    private ?DateInterval $refreshInterval = null;

    private ?string $productIdentifier = null;

    private ?string $source = null;

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
        return 'VCALENDAR';
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

    public function productIdentifier(string $identifier): Calendar
    {
        $this->productIdentifier = $identifier;

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

        $events = array_map(function ($eventToResolve) {
            if (! is_callable($eventToResolve)) {
                return $eventToResolve;
            }

            $newEvent = new Event();

            $eventToResolve($newEvent);

            return $newEvent;
        }, is_array($event) ? $event : [$event]);

        $this->events = array_merge($this->events, $events);

        return $this;
    }

    /**
     * @param $timezone \Spatie\IcalendarGenerator\Components\Timezone|array
     *
     * @return \Spatie\IcalendarGenerator\Components\Calendar
     */
    public function timezone($timezone)
    {
        if (is_null($timezone)) {
            return $this;
        }

        $this->timezones = array_merge(
            $this->timezones,
            is_array($timezone) ? $timezone : [$timezone]
        );

        return $this;
    }

    public function withoutTimezone(): Calendar
    {
        $this->withoutTimezone = true;

        return $this;
    }

    public function withoutAutoTimezoneComponents(): self
    {
        $this->withoutAutoTimezoneComponents = true;

        return $this;
    }

    public function refreshInterval(int $minutes): Calendar
    {
        $this->refreshInterval = new DateInterval("PT{$minutes}M");

        return $this;
    }

    /**
     * Identifies a location where a client can retrieve updated data for the calendar.
     *
     * @link https://datatracker.ietf.org/doc/html/rfc7986#section-5.7
     */
    public function source(string $source): self
    {
        $this->source = $source;

        return $this;
    }

    public function get(): string
    {
        return $this->toString();
    }

    public function getTimezoneRangeCollection(): TimezoneRangeCollection
    {
        return TimezoneRangeCollection::create()
            ->add(...array_map(
                fn (Event $event) => $event->getTimezoneRangeCollection(),
                $this->resolveEvents()
            ));
    }

    protected function payload(): ComponentPayload
    {
        return ComponentPayload::create($this->getComponentType())
            ->property(TextProperty::create('VERSION', '2.0'))
            ->property(TextProperty::create('PRODID', $this->productIdentifier ?? 'spatie/icalendar-generator'))
            ->optional(
                $this->name,
                fn () => TextProperty::create('NAME', $this->name)->addAlias('X-WR-CALNAME')
            )
            ->optional(
                $this->description,
                fn () => TextProperty::create('DESCRIPTION', $this->description)->addAlias('X-WR-CALDESC')
            )
            ->optional(
                $this->source,
                fn () => TextProperty::create('SOURCE', $this->source)->addParameter(new Parameter('VALUE', 'URI'))
            )
            ->optional(
                $this->refreshInterval,
                fn () => DurationProperty::create('REFRESH-INTERVAL', $this->refreshInterval)->addParameter(new Parameter('VALUE', 'DURATION'))
            )
            ->optional(
                $this->refreshInterval,
                fn () => DurationProperty::create('X-PUBLISHED-TTL', $this->refreshInterval)
            )
            ->subComponent(...$this->resolveTimezones())
            ->subComponent(...$this->resolveEvents());
    }

    private function resolveEvents(): array
    {
        if ($this->withoutTimezone === false) {
            return $this->events;
        }

        return array_map(
            fn (Event $event) => $event->withoutTimezone(),
            $this->events
        );
    }

    private function resolveTimezones(): array
    {
        if ($this->withoutAutoTimezoneComponents) {
            return $this->timezones;
        }

        $timezones = [];

        foreach ($this->getTimezoneRangeCollection()->get() as $timezoneIdentifier => ['min' => $min, 'max' => $max]) {
            $transitionsResolver = new TimezoneTransitionsResolver(
                new DateTimeZone($timezoneIdentifier),
                $min,
                $max
            );

            $entries = array_map(
                fn (TimezoneTransition $transition) => TimezoneEntry::createFromTransition($transition),
                $transitionsResolver->getTransitions()
            );

            $timezones[] = Timezone::create($timezoneIdentifier)->entry($entries);
        }

        return $timezones;
    }
}
