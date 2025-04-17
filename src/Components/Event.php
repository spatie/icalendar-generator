<?php

namespace Spatie\IcalendarGenerator\Components;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Spatie\IcalendarGenerator\ComponentPayload;
use Spatie\IcalendarGenerator\Enums\Classification;
use Spatie\IcalendarGenerator\Enums\Display;
use Spatie\IcalendarGenerator\Enums\EventStatus;
use Spatie\IcalendarGenerator\Enums\ParticipationStatus;
use Spatie\IcalendarGenerator\Properties\AppleLocationCoordinatesProperty;
use Spatie\IcalendarGenerator\Properties\BinaryProperty;
use Spatie\IcalendarGenerator\Properties\CalendarAddressProperty;
use Spatie\IcalendarGenerator\Properties\CoordinatesProperty;
use Spatie\IcalendarGenerator\Properties\DateTimeProperty;
use Spatie\IcalendarGenerator\Properties\Parameter;
use Spatie\IcalendarGenerator\Properties\RRuleProperty;
use Spatie\IcalendarGenerator\Properties\TextProperty;
use Spatie\IcalendarGenerator\Properties\UriProperty;
use Spatie\IcalendarGenerator\Timezones\HasTimezones;
use Spatie\IcalendarGenerator\Timezones\TimezoneRangeCollection;
use Spatie\IcalendarGenerator\ValueObjects\BinaryValue;
use Spatie\IcalendarGenerator\ValueObjects\CalendarAddress;
use Spatie\IcalendarGenerator\ValueObjects\DateTimeValue;
use Spatie\IcalendarGenerator\ValueObjects\RRule;

class Event extends Component implements HasTimezones
{
    /** @var Alert[] */
    protected array $alerts = [];

    protected ?DateTimeValue $starts = null;

    protected ?DateTimeValue $ends = null;

    protected DateTimeValue $created;

    protected ?string $description = null;

    protected ?string $address = null;

    protected ?string $addressName = null;

    protected ?string $googleConference = null;

    protected ?string $microsoftTeams = null;

    protected ?float $lat = null;

    protected ?float $lng = null;

    protected string $uuid;

    protected bool $withTimezone = true;

    protected bool $isFullDay = false;

    protected ?Classification $classification = null;

    protected ?bool $transparent = null;

    /** @var CalendarAddress[] */
    protected array $attendees = [];

    protected ?CalendarAddress $organizer = null;

    protected ?EventStatus $status = null;

    /** @var RRule|string|null */
    protected $rrule = null;

    protected ?DateTimeInterface $rruleStarting = null;

    protected ?DateTimeInterface $rruleUntil = null;

    /** @var DateTimeValue[] */
    protected array $recurrenceDates = [];

    /** @var DateTimeValue[] */
    public array $excludedRecurrenceDates = [];

    protected ?string $url = null;

    /** @var array<array{url: string, type: string|null}|BinaryValue> */
    protected array $attachments = [];

    /** @var array<array{url: string, type: string|null, display: Display|null}> */
    protected array $images = [];

    protected ?int $sequence = null;

    public static function create(?string $name = null): Event
    {
        return new self($name);
    }

    public function __construct(
        protected ?string $name = null
    ) {
        $this->uuid = uniqid();
        $this->created = DateTimeValue::create(new DateTimeImmutable())
            ->convertToTimezone(new \DateTimeZone('UTC'));
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
        if ($this->isFullDay) {
            if (method_exists($ends, 'modify')) {
                $ends = $ends->modify('+1 day');
            } else {
                throw new \LogicException('The provided DateTimeInterface instance does not support the modify method.');
            }

            $this->ends = DateTimeValue::create($ends, false);
        } else {
            $this->ends = DateTimeValue::create($ends, $withTime);
        }


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

    public function address(string $address, ?string $name = null): Event
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

    public function googleConference(string $googleConference): Event
    {
        $this->googleConference = $googleConference;

        return $this;
    }

    public function microsoftTeams(string $microsoftTeams): Event
    {
        $this->microsoftTeams = $microsoftTeams;

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
        $this->created = DateTimeValue::create($created, $withTime)
            ->convertToTimezone(new DateTimeZone('UTC'));

        return $this;
    }

    public function withoutTimezone(): Event
    {
        $this->withTimezone = false;

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

    public function alertAt(DateTimeInterface $alert, ?string $message = null): self
    {
        $this->alerts[] = Alert::date($alert, $message);

        return $this;
    }

    public function alertMinutesBefore(int $minutes, ?string $message = null): Event
    {
        $this->alerts[] = Alert::minutesBeforeStart($minutes, $message);

        return $this;
    }

    public function alertMinutesAfter(int $minutes, ?string $message = null): Event
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
        ?string $name = null,
        ?ParticipationStatus $participationStatus = null,
        bool $requiresResponse = false
    ): Event {
        $this->attendees[] = new CalendarAddress($email, $name, $participationStatus, $requiresResponse);

        return $this;
    }

    public function organizer(string $email, ?string $name = null): Event
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

    public function rruleAsString(string $rrule, ?DateTimeInterface $starting = null, ?DateTimeInterface $until = null): Event
    {
        $this->rrule = $rrule;
        $this->rruleStarting = $starting;
        $this->rruleUntil = $until;

        return $this;
    }

    /**
     * @param DateTimeInterface[]|DateTimeInterface $dates
     */
    public function doNotRepeatOn(array|DateTimeInterface $dates, bool $withTime = true): self
    {
        $dates = array_map(
            fn (DateTimeInterface $date) => DateTimeValue::create($date, $withTime),
            is_array($dates) ? $dates : [$dates]
        );

        $this->excludedRecurrenceDates = array_merge($this->excludedRecurrenceDates, $dates);

        return $this;
    }

    /**
     * @param DateTimeInterface[]|DateTimeInterface $dates
     */
    public function repeatOn(array|DateTimeInterface $dates, bool $withTime = true): self
    {
        $dates = array_map(
            fn (DateTimeInterface $date) => DateTimeValue::create($date, $withTime),
            is_array($dates) ? $dates : [$dates]
        );

        $this->recurrenceDates = array_merge($this->recurrenceDates, $dates);

        return $this;
    }

    public function url(string $url): Event
    {
        $this->url = $url;

        return $this;
    }

    public function attachment(string $url, ?string $mediaType = null): Event
    {
        $this->attachments[] = [
            'url' => $url,
            'type' => $mediaType,
        ];

        return $this;
    }

    public function embeddedAttachment(
        string $data,
        ?string $mediaType = null,
        bool $needsEncoding = true
    ): Event {
        $this->attachments[] = new BinaryValue($data, $mediaType, $needsEncoding);

        return $this;
    }

    public function image(string $url, ?string $mime = null, ?Display $display = null): Event
    {
        $this->images[] = [
            'url' => $url,
            'type' => $mime,
            'display' => $display,
        ];

        return $this;
    }

    public function sequence(int $sequence): Event
    {
        $this->sequence = $sequence;

        return $this;
    }

    public function getTimezoneRangeCollection(): TimezoneRangeCollection
    {
        if ($this->withTimezone === false) {
            return TimezoneRangeCollection::create();
        }

        return TimezoneRangeCollection::create()
            ->add($this->starts)
            ->add($this->ends)
            ->add($this->created)
            ->add(
                is_string($this->rrule)
                ? [$this->rruleStarting, $this->rruleUntil]
                : $this->rrule
            )
            ->add($this->recurrenceDates)
            ->add($this->excludedRecurrenceDates);
    }

    protected function payload(): ComponentPayload
    {
        $payload = ComponentPayload::create($this->getComponentType());

        $this
            ->resolveProperties($payload)
            ->resolveDateProperty($payload, $this->starts, 'DTSTART')
            ->resolveDateProperty($payload, $this->ends, 'DTEND')
            ->resolveLocationProperties($payload)
            ->resolveAlerts($payload);

        return $payload;
    }

    protected function resolveProperties(ComponentPayload $payload): self
    {
        $payload
            ->property(TextProperty::create('UID', $this->uuid))
            ->property(DateTimeProperty::create('DTSTAMP', $this->created));

        if ($this->name) {
            $payload->property(TextProperty::create('SUMMARY', $this->name));
        }

        if ($this->description) {
            $payload->property(TextProperty::create('DESCRIPTION', $this->description));
        }

        if ($this->address) {
            $payload->property(TextProperty::create('LOCATION', $this->address));
        }

        if ($this->classification) {
            $payload->property(TextProperty::createFromEnum('CLASS', $this->classification));
        }

        if ($this->status) {
            $payload->property(TextProperty::createFromEnum('STATUS', $this->status));
        }

        if ($this->googleConference) {
            $payload->property(TextProperty::create('X-GOOGLE-CONFERENCE', $this->googleConference));
        }

        if ($this->microsoftTeams) {
            $payload->property(TextProperty::create('X-MICROSOFT-SKYPETEAMSMEETINGURL', $this->microsoftTeams));
        }

        if ($this->transparent) {
            $payload->property(TextProperty::create('TRANSP', 'TRANSPARENT'));
        }

        if ($this->isFullDay) {
            $payload->property(TextProperty::create('X-MICROSOFT-CDO-ALLDAYEVENT', 'TRUE'));
        }

        if ($this->organizer) {
            $payload->property(CalendarAddressProperty::create('ORGANIZER', $this->organizer));
        }

        if ($this->rrule) {
            $property = is_string($this->rrule)
                ? TextProperty::create('RRULE', $this->rrule)->withoutEscaping()
                : RRuleProperty::create('RRULE', $this->rrule);

            $payload->property($property);
        }

        if ($this->url) {
            $payload->property(UriProperty::create('URL', $this->url));
        }

        if ($this->sequence) {
            $payload->property(TextProperty::create('SEQUENCE', (string) $this->sequence));
        }

        foreach ($this->attendees as $attendee) {
            $payload->property(CalendarAddressProperty::create('ATTENDEE', $attendee));
        }

        foreach ($this->recurrenceDates as $recurrenceDate) {
            $payload->property(self::dateTimePropertyWithSpecifiedType('RDATE', $recurrenceDate));
        }

        foreach ($this->excludedRecurrenceDates as $excludedRecurrenceDate) {
            $payload->property(self::dateTimePropertyWithSpecifiedType('EXDATE', $excludedRecurrenceDate));
        }

        foreach ($this->attachments as $attachment) {
            $property = match (true) {
                $attachment instanceof BinaryValue => BinaryProperty::create('ATTACH', $attachment),
                $attachment['type'] !== null => UriProperty::create('ATTACH', $attachment['url'])->addParameter(Parameter::create('FMTTYPE', $attachment['type'])),
                default => UriProperty::create('ATTACH', $attachment['url']),
            };

            $payload->property($property);
        }

        foreach ($this->images as $image) {
            $property = UriProperty::create('IMAGE', $image['url'])->addParameter(Parameter::create('VALUE', 'URI'));

            if ($image['type'] !== null) {
                $property->addParameter(Parameter::create('FMTTYPE', $image['type']));
            }

            if ($image['display'] !== null) {
                $property->addParameter(Parameter::create('DISPLAY', $image['display']));
            }

            $payload->property($property);
        }

        return $this;
    }

    protected static function dateTimePropertyWithSpecifiedType(
        string $name,
        DateTimeValue $dateTimeValue
    ): DateTimeProperty {
        $property = DateTimeProperty::create($name, $dateTimeValue);
        if ($dateTimeValue->hasTime()) {
            $property->addParameter(Parameter::create('VALUE', 'DATE-TIME'));
        }

        return $property;
    }

    protected function resolveDateProperty(ComponentPayload $payload, ?DateTimeValue $value, string $name): self
    {
        if ($value === null) {
            return $this;
        }

        $payload->property(
            DateTimeProperty::fromDateTime($name, $value->getDateTime(), ! $this->isFullDay, $this->withTimezone)
        );

        return $this;
    }

    protected function resolveLocationProperties(ComponentPayload $payload): self
    {
        if (is_null($this->lng) || is_null($this->lat)) {
            return $this;
        }

        $payload->property(CoordinatesProperty::create('GEO', $this->lat, $this->lng));

        if (is_null($this->address) || is_null($this->addressName)) {
            return $this;
        }

        $property = AppleLocationCoordinatesProperty::create(
            $this->lat,
            $this->lng,
            $this->address,
            $this->addressName
        );

        $payload->property($property);

        return $this;
    }

    protected function resolveAlerts(ComponentPayload $payload): self
    {
        $alerts = array_map(
            fn (Alert $alert) => $this->withTimezone ? $alert : $alert->withoutTimezone(),
            $this->alerts
        );

        $payload->subComponent(...$alerts);

        return $this;
    }
}
