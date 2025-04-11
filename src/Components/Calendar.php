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
    /** @var Event[] */
    protected array $events = [];

    /** @var Timezone[] */
    protected array $timezones = [];

    protected ?string $description = null;

    protected bool $withTimezone = true;

    protected bool $withAutoTimezoneComponents = true;

    protected ?DateInterval $refreshInterval = null;

    protected ?string $productIdentifier = null;

    protected ?string $source = null;

    public static function create(?string $name = null): self
    {
        return new self($name);
    }

    public function __construct(protected ?string $name = null)
    {
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

    public function name(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function description(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function productIdentifier(string $identifier): self
    {
        $this->productIdentifier = $identifier;

        return $this;
    }

    /**
     * @param Event|array<Event|Closure>|Closure $event
     */
    public function event(Event|array|Closure|null $event): self
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
     * @param Timezone|array<Timezone>|null $timezone
     */
    public function timezone(Timezone|array|null $timezone): self
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

    public function withoutTimezone(): self
    {
        $this->withTimezone = false;

        return $this;
    }

    public function withoutAutoTimezoneComponents(): self
    {
        $this->withAutoTimezoneComponents = false;

        return $this;
    }

    public function refreshInterval(int $minutes): self
    {
        $this->refreshInterval = new DateInterval("PT{$minutes}M");

        return $this;
    }

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
        $payload = ComponentPayload::create($this->getComponentType())
            ->property(TextProperty::create('VERSION', '2.0'))
            ->property(TextProperty::create('PRODID', $this->productIdentifier ?? 'spatie/icalendar-generator'));

        if ($this->name) {
            $payload->property(TextProperty::create('NAME', $this->name)->addAlias('X-WR-CALNAME'));
        }

        if ($this->description) {
            $payload->property(TextProperty::create('DESCRIPTION', $this->description)->addAlias('X-WR-CALDESC'));
        }

        if ($this->source) {
            $payload->property(TextProperty::create('SOURCE', $this->source)->addParameter(new Parameter('VALUE', 'URI')));
        }

        if ($this->refreshInterval) {
            $payload->property(DurationProperty::create('REFRESH-INTERVAL', $this->refreshInterval)->addParameter(new Parameter('VALUE', 'DURATION')));
            $payload->property(DurationProperty::create('X-PUBLISHED-TTL', $this->refreshInterval));
        }

        return $payload
            ->subComponent(...$this->resolveTimezones())
            ->subComponent(...$this->resolveEvents());
    }

    /**
     * @return Event[]
     */
    protected function resolveEvents(): array
    {
        if ($this->withTimezone === true) {
            return $this->events;
        }

        return array_map(
            fn (Event $event) => $event->withoutTimezone(),
            $this->events
        );
    }

    /**
     * @return Timezone[]
     */
    protected function resolveTimezones(): array
    {
        if ($this->withAutoTimezoneComponents === false) {
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
