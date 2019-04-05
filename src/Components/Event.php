<?php

namespace Spatie\Calendar\Components;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Spatie\Calendar\ComponentPayload;
use Spatie\Calendar\HasSubComponents;

class Event extends Component
{
    use HasSubComponents;

    /** @var DateTimeInterface */
    protected $starts;

    /** @var DateTimeInterface */
    protected $ends;

    /** @var string */
    protected $name;

    /** @var string|null */
    protected $description;

    /** @var string|null */
    protected $location;

    /** @var string */
    protected $uid;

    /** @var DateTimeInterface */
    protected $created;

    /** @var bool */
    protected $withTimezone = false;

    /** @var bool */
    protected $isFullDay = false;

    public function getComponentType(): string
    {
        return 'EVENT';
    }

    public function getRequiredProperties(): array
    {
        return [
            'UID',
            'DTSTAMP',
            'DTSTART',
        ];
    }

    public static function create(?string $name = null): Event
    {
        return new self($name);
    }

    public function __construct(?string $name = null)
    {
        $this->name = $name;
        $this->uid = uniqid();
        $this->created = new DateTimeImmutable();
    }

    public function starts(DateTimeInterface $starts): Event
    {
        $this->starts = $starts;

        return $this;
    }

    public function ends(DateTimeInterface $ends): Event
    {
        $this->ends = $ends;

        return $this;
    }

    public function period(DateTimeInterface $starts, DateTimeInterface $ends): Event
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

    public function uniqueIdentifier(string $uid): Event
    {
        $this->uid = $uid;

        return $this;
    }

    public function created(DateTimeInterface $created): Event
    {
        $this->created = $created;

        return $this;
    }

    public function withTimezone(): Event
    {
        $this->withTimezone = true;

        return $this;
    }

    public function fullDay(): Event
    {
        $this->isFullDay = true;

        return $this;
    }

    public function alarm($alarm): Event
    {
        $this->addSubComponent($alarm);

        return $this;
    }

    public function getPayload(): ComponentPayload
    {
        return ComponentPayload::new($this->getComponentType())
            ->textProperty('UID', $this->uid)
            ->textProperty('SUMMARY', $this->name)
            ->textProperty('DESCRIPTION', $this->description)
            ->textProperty('LOCATION', $this->location)
            ->dateTimeProperty(
                'DTSTART',
                $this->starts,
                ! $this->isFullDay,
                $this->withTimezone
            )
            ->dateTimeProperty(
                'DTEND',
                $this->ends,
                ! $this->isFullDay,
                $this->withTimezone
            )
            ->dateTimeProperty(
                'DTSTAMP',
                $this->created,
                true,
                $this->withTimezone
            )
            ->subComponent(...$this->subComponents);
    }
}
