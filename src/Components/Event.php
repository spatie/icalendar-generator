<?php

namespace Spatie\IcalendarGenerator\Components;

use DateTimeImmutable;
use DateTimeInterface;
use Spatie\IcalendarGenerator\ComponentPayload;
use Spatie\IcalendarGenerator\Enums\Classification;
use Spatie\IcalendarGenerator\Enums\EventStatus;
use Spatie\IcalendarGenerator\Enums\ParticipationStatus;
use Spatie\IcalendarGenerator\Properties\CalendarAddressProperty;
use Spatie\IcalendarGenerator\Properties\CoordinatesProperty;
use Spatie\IcalendarGenerator\Properties\DateTimeProperty;
use Spatie\IcalendarGenerator\Properties\Parameter;
use Spatie\IcalendarGenerator\Properties\RRuleProperty;
use Spatie\IcalendarGenerator\Properties\TextProperty;
use Spatie\IcalendarGenerator\ValueObjects\CalendarAddress;
use Spatie\IcalendarGenerator\ValueObjects\RRule;

class Event extends Component
{
    private array $alerts = [];

    private ?DateTimeInterface $starts = null;

    private ?DateTimeInterface $ends = null;

    private ?string $name = null;

    private ?string $description = null;

    private ?string $address = null;

    private ?string $addressName = null;

    private ?float $lat = null;

    private ?float $lng = null;

    private string $uuid;

    private DateTimeInterface $created;

    private bool $withTimezone = false;

    private bool $isFullDay = false;

    private ?Classification $classification = null;

    private ?bool $transparent = null;

    private array $attendees = [];

    private ?CalendarAddress $organizer = null;

    private ?EventStatus $status = null;

    private ?RRule $rrule = null;

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

    public function rrule(RRule $rrule): Event
    {
        $this->rrule = $rrule;

        return $this;
    }

    protected function payload(): ComponentPayload
    {
        $payload = ComponentPayload::create($this->getComponentType())
            ->property(TextProperty::create('UID', $this->uuid))
            ->property(DateTimeProperty::create('DTSTAMP', $this->created, true, $this->withTimezone))
            ->optional(
                $this->name,
                fn () => TextProperty::create('SUMMARY', $this->name)
            )
            ->optional(
                $this->description,
                fn () => TextProperty::create('DESCRIPTION', $this->description)
            )
            ->optional(
                $this->address,
                fn () => TextProperty::create('LOCATION', $this->address)
            )
            ->optional(
                $this->classification,
                fn () => TextProperty::create('CLASS', $this->classification->value)
            )
            ->optional(
                $this->status,
                fn () => TextProperty::create('STATUS', $this->status->value)
            )
            ->optional(
                $this->transparent,
                fn () => TextProperty::create('TRANSP', 'TRANSPARENT')
            )
            ->optional(
                $this->starts,
                fn () => DateTimeProperty::create('DTSTART', $this->starts, ! $this->isFullDay, $this->withTimezone)
            )
            ->optional(
                $this->ends,
                fn () => DateTimeProperty::create('DTEND', $this->ends, ! $this->isFullDay, $this->withTimezone)
            )
            ->optional(
                $this->organizer,
                fn () => CalendarAddressProperty::create('ORGANIZER', $this->organizer)
            )
            ->optional(
                $this->rrule,
                fn () => RRuleProperty::create('RRULE', $this->rrule)
            )
            ->multiple(
                $this->attendees,
                fn (CalendarAddress $attendee) => CalendarAddressProperty::create('ATTENDEE', $attendee)
            )
            ->subComponent(...$this->alerts);

        return $this->resolveLocationProperties($payload);
    }

    private function resolveLocationProperties(ComponentPayload $payload): ComponentPayload
    {
        if (is_null($this->lng) && is_null($this->lat)) {
            return $payload;
        }

        $payload->property(CoordinatesProperty::create('GEO', $this->lat, $this->lng));

        if (is_null($this->address) || is_null($this->addressName)) {
            return $payload;
        }

        $property = CoordinatesProperty::create(
            'X-APPLE-STRUCTURED-LOCATION',
            $this->lat,
            $this->lng
        )->addParameter(Parameter::create('VALUE', 'URI'))
            ->addParameter(Parameter::create('X-ADDRESS', $this->address))
            ->addParameter(Parameter::create('X-APPLE-RADIUS', 72))
            ->addParameter(Parameter::create('X-TITLE', $this->addressName));

        $payload->property($property);

        return $payload;
    }
}
