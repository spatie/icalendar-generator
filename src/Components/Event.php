<?php

namespace Spatie\Calendar\Components;

use DateTime;
use Spatie\Calendar\ComponentPayload;

class Event extends Component
{
    /** @var DateTime */
    protected $starts;

    /** @var DateTime */
    protected $ends;

    /** @var string */
    protected $name;

    /** @var string|null */
    protected $description;

    /** @var string|null */
    protected $location;

    /** @var string */
    protected $uuid;

    /** @var DateTime */
    protected $created;

    /** @var bool */
    protected $withTimezone;

    public function getComponentType(): string
    {
        return 'EVENT';
    }

    public function getRequiredProperties(): array
    {
        return [
            'UID',
            'DTSTAMP',
            'DTSTART'
        ];
    }

    public static function new(?string $name = null): Event
    {
        return new self($name);
    }

    public function __construct(?string $name = null)
    {
        $this->name = $name;
        $this->uuid = uniqid();
        $this->created = new DateTime();
    }

    public function starts(DateTime $starts): Event
    {
        $this->starts = $starts;

        return $this;
    }

    public function ends(DateTime $ends): Event
    {
        $this->ends = $ends;

        return $this;
    }

    public function period(DateTime $starts, DateTime $ends): Event
    {
        $this->starts = $starts;
        $this->ends = $ends;

        return $this;
    }


    public function name(string $name): Event
    {
        $this->name = $name;

        return $this;
    }

    public function description(string $description): Event
    {
        $this->description = $description;

        return $this;
    }

    public function location(string $location): Event
    {
        $this->location = $location;

        return $this;
    }

    public function uuid(string $uuid): Event
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function created(DateTime $created): Event
    {
        $this->created = $created;

        return $this;
    }

    public function withTimezone(): Event
    {
        $this->withTimezone = true;

        return $this;
    }

    public function getPayload(): ComponentPayload
    {
        return ComponentPayload::new($this->getComponentType())
            ->textProperty('UID', $this->uuid)
            ->textProperty('SUMMARY', $this->name)
            ->textProperty('DESCRIPTION', $this->description)
            ->textProperty('LOCATION', $this->location)
            ->dateTimeProperty('DTSTART', $this->starts)
            ->dateTimeProperty('DTEND', $this->ends)
            ->dateTimeProperty('DTSTAMP', $this->created);
    }
}
