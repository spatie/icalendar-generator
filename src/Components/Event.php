<?php

namespace Spatie\IcalendarGenerator\Components;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Spatie\IcalendarGenerator\ComponentPayload;
use Spatie\IcalendarGenerator\Enums\Classification;
use Spatie\IcalendarGenerator\Enums\Display;
use Spatie\IcalendarGenerator\Enums\EventStatus;
use Spatie\IcalendarGenerator\Enums\ParticipationStatus;
use Spatie\IcalendarGenerator\Properties\AppleLocationCoordinatesProperty;
use Spatie\IcalendarGenerator\Properties\CalendarAddressProperty;
use Spatie\IcalendarGenerator\Properties\CoordinatesProperty;
use Spatie\IcalendarGenerator\Properties\DateTimeProperty;
use Spatie\IcalendarGenerator\Properties\Parameter;
use Spatie\IcalendarGenerator\Properties\RRuleProperty;
use Spatie\IcalendarGenerator\Properties\TextProperty;
use Spatie\IcalendarGenerator\Properties\UriProperty;
use Spatie\IcalendarGenerator\Timezones\HasTimezones;
use Spatie\IcalendarGenerator\Timezones\TimezoneRangeCollection;
use Spatie\IcalendarGenerator\ValueObjects\CalendarAddress;
use Spatie\IcalendarGenerator\ValueObjects\DateTimeValue;
use Spatie\IcalendarGenerator\ValueObjects\RRule;

class Event extends Component implements HasTimezones
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

    private ?string $googleConference = null;

    private ?string $microsoftTeams = null;

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

    /** @var \Spatie\IcalendarGenerator\ValueObjects\DateTimeValue[] */
    private array $recurrence_dates = [];

    /** @var \Spatie\IcalendarGenerator\ValueObjects\DateTimeValue[] */
    public array $excluded_recurrence_dates = [];

    private ?string $url = null;

    /** @var array[] */
    private array $attachments = [];

    /** @var array[] */
    private array $images = [];

    public static function create(string $name = null): Event
    {
        return new self($name);
    }

    public function __construct(string $name = null)
    {
        $this->name = $name;
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
        ParticipationStatus $participationStatus = null,
        bool $requiresResponse = false
    ): Event {
        $this->attendees[] = new CalendarAddress($email, $name, $participationStatus, $requiresResponse);

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

    /**
     * @param DateTimeInterface[]|DateTimeInterface $dates
     * @param bool $withTime
     *
     * @return \Spatie\IcalendarGenerator\Components\Event
     */
    public function doNotRepeatOn($dates, bool $withTime = true): self
    {
        $dates = array_map(
            fn (DateTime $date) => DateTimeValue::create($date, $withTime),
            is_array($dates) ? $dates : [$dates]
        );

        $this->excluded_recurrence_dates = array_merge($this->excluded_recurrence_dates, $dates);

        return $this;
    }

    /**
     * @param DateTimeInterface[]|DateTimeInterface $dates
     * @param bool $withTime
     *
     * @return \Spatie\IcalendarGenerator\Components\Event
     */
    public function repeatOn($dates, bool $withTime = true): self
    {
        $dates = array_map(
            fn (DateTime $date) => DateTimeValue::create($date, $withTime),
            is_array($dates) ? $dates : [$dates]
        );

        $this->recurrence_dates = array_merge($this->recurrence_dates, $dates);

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

    public function image(string $url, ?string $mime = null, ?Display $display = null): Event
    {
        $this->images[] = [
            'url' => $url,
            'type' => $mime,
            'display' => $display,
        ];

        return $this;
    }

    public function getTimezoneRangeCollection(): TimezoneRangeCollection
    {
        if ($this->withoutTimezone) {
            return TimezoneRangeCollection::create();
        }

        return TimezoneRangeCollection::create()
            ->add($this->starts)
            ->add($this->ends)
            ->add($this->created)
            ->add($this->rrule)
            ->add($this->recurrence_dates)
            ->add($this->excluded_recurrence_dates);
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

    private function resolveProperties(ComponentPayload $payload): self
    {
        $payload
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
                fn () => TextProperty::createFromEnum('CLASS', $this->classification)
            )
            ->optional(
                $this->status,
                fn () => TextProperty::createFromEnum('STATUS', $this->status)
            )
            ->optional(
                $this->googleConference,
                fn () => TextProperty::create('X-GOOGLE-CONFERENCE', $this->googleConference)
            )
            ->optional(
                $this->microsoftTeams,
                fn () => TextProperty::create('X-MICROSOFT-SKYPETEAMSMEETINGURL', $this->microsoftTeams)
            )
            ->optional(
                $this->transparent,
                fn () => TextProperty::create('TRANSP', 'TRANSPARENT')
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
            ->multiple(
                $this->recurrence_dates,
                fn (DateTimeValue $dateTime) => self::dateTimePropertyWithSpecifiedType('RDATE', $dateTime)
            )
            ->multiple(
                $this->excluded_recurrence_dates,
                fn (DateTimeValue $dateTime) => self::dateTimePropertyWithSpecifiedType('EXDATE', $dateTime)
            )
            ->multiple(
                $this->attachments,
                fn (array $attachment) => $attachment['type'] !== null
                    ? UriProperty::create('ATTACH', $attachment['url'])->addParameter(Parameter::create('FMTTYPE', $attachment['type']))
                    : UriProperty::create('ATTACH', $attachment['url'])
            )
            ->multiple(
                $this->images,
                function (array $image) {
                    $property = UriProperty::create('IMAGE', $image['url'])->addParameter(Parameter::create('VALUE', 'URI'));

                    if ($image['type'] !== null) {
                        $property->addParameter(Parameter::create('FMTTYPE', $image['type']));
                    }

                    if ($image['display'] !== null) {
                        $property->addParameter(Parameter::create('DISPLAY', $image['display']));
                    }

                    return $property;
                }
            );

        return $this;
    }

    private static function dateTimePropertyWithSpecifiedType(
        string $name,
        DateTimeValue $dateTimeValue
    ): DateTimeProperty {
        $property = DateTimeProperty::create($name, $dateTimeValue);
        if ($dateTimeValue->hasTime()) {
            $property->addParameter(Parameter::create('VALUE', 'DATE-TIME'));
        }

        return $property;
    }

    private function resolveDateProperty(ComponentPayload $payload, ?DateTimeValue $value, string $name): self
    {
        if ($value === null) {
            return $this;
        }

        $payload->property(
            DateTimeProperty::fromDateTime($name, $value->getDateTime(), ! $this->isFullDay, $this->withoutTimezone)
        );

        return $this;
    }

    private function resolveLocationProperties(ComponentPayload $payload): self
    {
        if (is_null($this->lng) && is_null($this->lat)) {
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

    private function resolveAlerts(ComponentPayload $payload): self
    {
        $alerts = array_map(
            fn (Alert $alert) => $this->withoutTimezone ? $alert->withoutTimezone() : $alert,
            $this->alerts
        );

        $payload->subComponent(...$alerts);

        return $this;
    }
}
