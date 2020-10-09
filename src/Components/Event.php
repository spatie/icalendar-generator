<?php

namespace Spatie\IcalendarGenerator\Components;

use DateTimeImmutable;
use DateTimeInterface;
use Spatie\IcalendarGenerator\ComponentPayload;
use Spatie\IcalendarGenerator\Enums\Classification;
use Spatie\IcalendarGenerator\Enums\EventStatus;
use Spatie\IcalendarGenerator\Enums\ParticipationStatus;
use Spatie\IcalendarGenerator\PropertyTypes\CalendarAddressPropertyType;
use Spatie\IcalendarGenerator\PropertyTypes\CoordinatesPropertyType;
use Spatie\IcalendarGenerator\PropertyTypes\Parameter;
use Spatie\IcalendarGenerator\ValueObjects\CalendarAddress;

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
    private $address;

    /** @var string|null */
    private $addressName;

    /** @var float|null */
    private $lat;

    /** @var float|null */
    private $lng;

    /** @var string */
    private $uuid;

    /** @var \DateTimeInterface */
    private $created;

    /** @var bool */
    private $withTimezone = false;

    /** @var bool */
    private $isFullDay = false;

    /** @var \Spatie\IcalendarGenerator\Enums\Classification|null */
    private $classification = null;

    /** @var bool|null */
    private $transparent = null;

    /** @var \Spatie\IcalendarGenerator\ValueObjects\CalendarAddress[] */
    private $attendees = [];

    /** @var \Spatie\IcalendarGenerator\ValueObjects\CalendarAddress|null */
    private $organizer = null;

    /** @var \Spatie\IcalendarGenerator\Enums\EventStatus|null */
    private $status = null;

    /** @var string|null */
    private $url;

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

    public function address(string $address, string $name = null): Event
    {
        $this->address = $address;

        if ($name) {
            $this->addressName = $name;
        }

        return $this;
    }

    public function addressName(string $name): Event
    {
        $this->addressName = $name;

        return $this;
    }

    public function coordinates(float $lat, float $lng): Event
    {
        $this->lat = $lat;
        $this->lng = $lng;

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

    public function alert(Alert $alert): Event
    {
        $this->alerts[] = $alert;

        return $this;
    }

    public function alertMinutesBefore(int $minutes, string $message = null): Event
    {
        $this->alerts[] = Alert::minutesBeforeStart($minutes, $message);

        return $this;
    }

    public function alertMinutesAfter(int $minutes, string $message = null): Event
    {
        $this->alerts[] = Alert::minutesAfterEnd($minutes, $message);

        return $this;
    }

    public function classification(?Classification $classification): Event
    {
        $this->classification = $classification;

        return $this;
    }

    public function transparent(): Event
    {
        $this->transparent = true;

        return $this;
    }

    public function attendee(
        string $email,
        string $name = null,
        ParticipationStatus $participationStatus = null
    ): Event {
        $this->attendees[] = new CalendarAddress($email, $name, $participationStatus);

        return $this;
    }

    public function organizer(string $email, string $name = null): Event
    {
        $this->organizer = new CalendarAddress($email, $name);

        return $this;
    }

    public function status(EventStatus $status): Event
    {
        $this->status = $status;

        return $this;
    }

    public function url(string $url): Event
    {
        $this->url = $url;

        return $this;
    }

    protected function payload(): ComponentPayload
    {
        $payload = ComponentPayload::create($this->getComponentType())
            ->textProperty('UID', $this->uuid)
            ->textProperty('SUMMARY', $this->name)
            ->textProperty('DESCRIPTION', $this->description)
            ->textProperty('LOCATION', $this->address)
            ->textProperty('CLASS', $this->classification)
            ->textProperty('TRANSP', $this->transparent ? 'TRANSPARENT' : null)
            ->textProperty('STATUS', $this->status)
            ->uriProperty('URL', $this->url)
            ->dateTimeProperty('DTSTART', $this->starts, ! $this->isFullDay, $this->withTimezone)
            ->dateTimeProperty('DTEND', $this->ends, ! $this->isFullDay, $this->withTimezone)
            ->dateTimeProperty('DTSTAMP', $this->created, true, $this->withTimezone)
            ->subComponent(...$this->alerts);

        if ($this->organizer) {
            $payload->property(CalendarAddressPropertyType::create('ORGANIZER', $this->organizer));
        }

        foreach ($this->attendees as $attendee) {
            $payload->property(CalendarAddressPropertyType::create('ATTENDEE', $attendee));
        }

        $payload = $this->resolveLocationProperties($payload);

        return $payload;
    }

    private function resolveLocationProperties(ComponentPayload $payload): ComponentPayload
    {
        if (is_null($this->lng) && is_null($this->lat)) {
            return $payload;
        }

        $payload->property(CoordinatesPropertyType::create('GEO', $this->lat, $this->lng));

        if (is_null($this->address) || is_null($this->addressName)) {
            return $payload;
        }

        $property = CoordinatesPropertyType::create(
            'X-APPLE-STRUCTURED-LOCATION',
            $this->lat,
            $this->lng
        )->addParameter(Parameter::create('VALUE', 'URI'))
            ->addParameter(Parameter::create('X-ADDRESS', $this->address))
            ->addParameter(Parameter::create('X-APPLE-RADIUS', '72'))
            ->addParameter(Parameter::create('X-TITLE', $this->addressName));

        $payload->property($property);

        return $payload;
    }
}
