<?php

namespace Spatie\IcalendarGenerator\Components;

use DateInterval;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Exception;
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
use Spatie\IcalendarGenerator\Properties\UriProperty;
use Spatie\IcalendarGenerator\ValueObjects\CalendarAddress;
use Spatie\IcalendarGenerator\ValueObjects\DateTimeValue;
use Spatie\IcalendarGenerator\ValueObjects\DurationValue;
use Spatie\IcalendarGenerator\ValueObjects\RRule;

class Event extends Component
{
    /** @var \Spatie\IcalendarGenerator\Components\Alert[] */
    private array $alerts = [];

    private ?DateTimeValue $starts = null;

    private ?DateTimeValue $ends = null;

    private DateTimeValue $created;

    private ?string $name = null;

    private ?string $description = null;

    private ?string $address = null;

    private ?string $addressName = null;

    private ?float $lat = null;

    private ?float $lng = null;

    private string $uuid;

    private bool $withoutTimezone = false;

    private bool $isFullDay = false;

    private ?Classification $classification = null;

    private ?bool $transparent = null;

    private array $attendees = [];

    private ?CalendarAddress $organizer = null;

    private ?EventStatus $status = null;

    private ?RRule $rrule = null;

    /** @var \Spatie\IcalendarGenerator\ValueObjects\DateTimeValue[]|\Spatie\IcalendarGenerator\ValueObjects\PeriodValue[]|\Spatie\IcalendarGenerator\ValueObjects\DurationValue[] */
    private array $recurrence_dates = [];

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
        $this->created = DateTimeValue::create(new DateTimeImmutable());
    }

    public function getComponentType(): string
    {
        return 'VEVENT';
    }

    public function getRequiredProperties(): array
    {
        return [
            'UID',
            'DTSTAMP',
            'DTSTART',
        ];
    }

    public function startsAt(DateTimeInterface $starts, bool $withTime = true): Event
    {
        $this->starts = DateTimeValue::create($starts, $withTime);

        return $this;
    }

    public function endsAt(DateTimeInterface $ends, bool $withTime = true): Event
    {
        $this->ends = DateTimeValue::create($ends, $withTime);

        return $this;
    }

    public function period(
        DateTimeInterface $starts,
        DateTimeInterface $ends,
        bool $withTime = true
    ): Event {
        $this->starts = DateTimeValue::create($starts, $withTime);
        $this->ends = DateTimeValue::create($ends, $withTime);

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

    public function createdAt(DateTimeInterface $created, bool $withTime = true): Event
    {
        $this->created = new DateTimeValue($created, $withTime);

        return $this;
    }

    public function withoutTimezone(): Event
    {
        $this->withoutTimezone = true;

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

    public function alertAt(DateTimeInterface $alert, string $message = null)
    {
        $this->alerts[] = Alert::date($alert, $message);

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

    public function reccur($first, bool $withTime = true)
    {
        if ($first instanceof DateInterval) {
            $this->recurrence_dates[] = DurationValue::create($first);

            return;
        }

        if ($first instanceof DateTime) {
            $this->recurrence_dates[] = DateTimeValue::create($first, $withTime);

            return;
        }

        throw new Exception("Could not reccur");
    }

    public function url(string $url): Event
    {
        $this->url = $url;

        return $this;
    }

    protected function payload(): ComponentPayload
    {
        if ($this->isFullDay) {
            $this->starts = DateTimeValue::create($this->starts->getDateTime(), false);
            $this->ends = DateTimeValue::create($this->ends->getDateTime(), false);
        }

        $payload = ComponentPayload::create($this->getComponentType())
            ->property(TextProperty::create('UID', $this->uuid))
            ->property(DateTimeProperty::create('DTSTAMP', $this->created, $this->withoutTimezone))
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
                fn () => DateTimeProperty::create('DTSTART', $this->starts, $this->withoutTimezone)
            )
            ->optional(
                $this->ends,
                fn () => DateTimeProperty::create('DTEND', $this->ends, $this->withoutTimezone)
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
            ->optional(
                $this->url,
                fn () => UriProperty::create('URL', $this->url)
            )
            ->subComponent(...$this->resolveAlerts());

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

    private function resolveAlerts(): array
    {
        if ($this->withoutTimezone === false) {
            return $this->alerts;
        }

        return array_map(
            fn (Alert $alert) => $alert->withoutTimezone(),
            $this->alerts
        );
    }
}
