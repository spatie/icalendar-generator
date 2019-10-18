<?php

namespace Spatie\IcalendarGenerator\Components;

use DateTime;
use DateInterval;
use DateTimeImmutable;
use DateTimeInterface;
use Spatie\IcalendarGenerator\ComponentPayload;

final class Event extends Component
{
    /** @var array */
    private $alerts = [];

    /** @var \DateTimeInterface */
    private $starts;

    /** @var \DateTimeInterface */
    private $ends;

    /** @var string */
    private $name;

    /** @var string|null */
    private $description;

    /** @var string|null */
    private $location;

    /** @var string */
    private $uuid;

    /** @var \DateTimeInterface */
    private $created;

    /** @var bool */
    private $withTimezone = false;

    /** @var bool */
    private $isFullDay = false;

    public static function create(string $name = null): Event
    {
        return new self($name);
    }

    public function __construct(string $name = null)
    {
        $this->name = $name;
        $this->uuid = uniqid();
        $this->created = new DateTimeImmutable();
    }

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

    public function startsAt(DateTimeInterface $starts): Event
    {
        $this->starts = $starts;

        return $this;
    }

    public function endsAt(DateTimeInterface $ends): Event
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
        $this->uuid = $uid;

        return $this;
    }

    public function createdAt(DateTimeInterface $created): Event
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

    public function alertMinutesBefore(int $minutes, string $message = null): Event
    {
        $trigger = clone $this->starts;

        $this->alerts[] = Alert::create(
            $trigger->sub(new DateInterval("PT{$minutes}M")),
            $message ?? $this->name
        );

        return $this;
    }

    public function getPayload(): ComponentPayload
    {
        return ComponentPayload::create($this->getComponentType())
            ->textProperty('UID', $this->uuid)
            ->textProperty('SUMMARY', $this->name)
            ->textProperty('DESCRIPTION', $this->description)
            ->textProperty('LOCATION', $this->location)
            ->dateTimeProperty('DTSTART', $this->starts, ! $this->isFullDay, $this->withTimezone)
            ->dateTimeProperty('DTEND', $this->ends, ! $this->isFullDay, $this->withTimezone)
            ->dateTimeProperty('DTSTAMP', $this->created, true, $this->withTimezone)
            ->subComponent(...$this->alerts);
    }
}
