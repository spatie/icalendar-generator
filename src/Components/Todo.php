<?php

namespace Spatie\IcalendarGenerator\Components;

use DateInterval;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use LogicException;
use Spatie\IcalendarGenerator\ComponentPayload;
use Spatie\IcalendarGenerator\Enums\Classification;
use Spatie\IcalendarGenerator\Enums\ParticipationStatus;
use Spatie\IcalendarGenerator\Enums\EventStatus;
use Spatie\IcalendarGenerator\Properties\BinaryProperty;
use Spatie\IcalendarGenerator\Properties\CalendarAddressProperty;
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

class Todo extends Component implements HasTimezones
{

    use Alerts;

    protected ?string $name = null;

    protected string $uuid;

    protected DateTimeValue $created;

    protected ?DateTimeValue $dtstart = null;

    protected ?DateTimeValue $due = null;

    protected ?string $duration = null; // RFC5545 duration string (e.g. "P1D")

    protected ?string $description = null;

    protected ?Classification $classification = null;

    protected ?DateTimeValue $completed = null;

    protected ?int $percentComplete = null;

    protected ?int $priority = null;

    protected ?CalendarAddress $organizer = null;

    /** @var CalendarAddress[] */
    protected array $attendees = [];

    protected ?EventStatus $status = null;

    /** @var RRule|string|null */
    protected $rrule = null;

    protected ?DateTimeInterface $rruleStarting = null;

    protected ?DateTimeInterface $rruleUntil = null;

    /** @var DateTimeValue[] */
    protected array $recurrenceDates = [];

    /** @var DateTimeValue[] */
    public array $excludedRecurrenceDates = [];

    /** @var array<array{url: string, type: string|null}|BinaryValue> */
    protected array $attachments = [];

    protected bool $withTimezone = true;

    protected ?int $sequence = null;

    public static function create(?string $summary = null): Todo
    {
        return new self($summary);
    }

    public function __construct(?string $summary = null)
    {
        $this->name = $summary;
        $this->uuid = uniqid();
        $this->created = DateTimeValue::create(new DateTimeImmutable())
            ->convertToTimezone(new DateTimeZone('UTC'));
    }

    public function getComponentType(): string
    {
        return 'VTODO';
    }

    public function getRequiredProperties(): array
    {
        return [
            'UID',
            'DTSTAMP',
        ];
    }

    public function summary(string $summary): self
    {
        $this->name = $summary;
        return $this;
    }

    public function description(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function uniqueIdentifier(string $uid): self
    {
        $this->uuid = $uid;
        return $this;
    }

    public function createdAt(DateTimeInterface $created, bool $withTime = true): self
    {
        $this->created = DateTimeValue::create($created, $withTime)
            ->convertToTimezone(new DateTimeZone('UTC'));
        return $this;
    }

    public function withoutTimezone(): self
    {
        $this->withTimezone = false;
        return $this;
    }

    public function dtstart(DateTimeInterface $dtstart, bool $withTime = true): self
    {
        $this->dtstart = DateTimeValue::create($dtstart, $withTime);
        return $this;
    }

    public function dueAt(DateTimeInterface $due, bool $withTime = true): self
    {
        if ($this->duration !== null) {
            throw new LogicException('DUE and DURATION MUST NOT both be set on a VTODO.');
        }

        $this->due = DateTimeValue::create($due, $withTime);
        return $this;
    }

    /**
     * Accepts DateInterval or an RFC duration string (e.g. "P1D")
     */
    public function duration(DateInterval|string $duration): self
    {
        if ($this->due !== null) {
            throw new LogicException('DUE and DURATION MUST NOT both be set on a VTODO.');
        }

        if ($duration instanceof DateInterval) {
            // Convert DateInterval to RFC5545 duration string roughly via format
            $spec = $duration->format('P%yY%mM%dDT%hH%iM%sS');
            $this->duration = $spec;
        } else {
            $this->duration = $duration;
        }

        return $this;
    }

    public function completedAt(DateTimeInterface $completed, bool $withTime = true): self
    {
        $this->completed = DateTimeValue::create($completed, $withTime);
        return $this;
    }

    public function classification(?Classification $classification): self
    {
        $this->classification = $classification;
        return $this;
    }

    public function percentComplete(int $percent): self
    {
        $this->percentComplete = $percent;
        return $this;
    }

    public function priority(int $priority): self
    {
        $this->priority = $priority;
        return $this;
    }

    public function organizer(string $email, ?string $name = null): self
    {
        $this->organizer = new CalendarAddress($email, $name);
        return $this;
    }

    public function attendee(
        string $email,
        ?string $name = null,
        ?ParticipationStatus $participationStatus = null,
        bool $requiresResponse = false
    ): self {
        $this->attendees[] = new CalendarAddress($email, $name, $participationStatus, $requiresResponse);
        return $this;
    }

    public function status(EventStatus $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function rrule(RRule $rrule): self
    {
        $this->rrule = $rrule;
        return $this;
    }

    public function rruleAsString(string $rrule, ?DateTimeInterface $starting = null, ?DateTimeInterface $until = null): self
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

    public function url(string $url): self
    {
        $this->attachments[] = ['url' => $url, 'type' => null];
        // Also set URL property separately
        $this->attachments[] = ['url' => $url, 'type' => null]; // keep consistent with Event behaviour for ATTACH; URL added below as property
        $this->url = $url ?? null;
        return $this;
    }

    protected ?string $url = null;

    public function attachment(string $url, ?string $mediaType = null): self
    {
        $this->attachments[] = [
            'url' => $url,
            'type' => $mediaType,
        ];
        return $this;
    }

    public function embeddedAttachment(string $data, ?string $mediaType = null, bool $needsEncoding = true): self
    {
        $this->attachments[] = new BinaryValue($data, $mediaType, $needsEncoding);
        return $this;
    }

    public function sequence(int $sequence): self
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
            ->add($this->dtstart)
            ->add($this->due)
            ->add($this->created)
            ->add(
                is_string($this->rrule)
                    ? [$this->rruleStarting, $this->rruleUntil]
                    : $this->rrule
            )
            ->add($this->recurrenceDates)
            ->add($this->excludedRecurrenceDates)
            ->add($this->completed);
    }

    protected function payload(): ComponentPayload
    {
        $payload = ComponentPayload::create($this->getComponentType());

        $this
            ->resolveProperties($payload)
            ->resolveDateProperty($payload, $this->dtstart, 'DTSTART')
            ->resolveDateProperty($payload, $this->due, 'DUE')
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

        if ($this->classification) {
            $payload->property(TextProperty::createFromEnum('CLASS', $this->classification));
        }

        if ($this->completed) {
            $payload->property(DateTimeProperty::fromDateTime('COMPLETED', $this->completed->getDateTime(), true, $this->withTimezone));
        }

        if ($this->percentComplete !== null) {
            $payload->property(TextProperty::create('PERCENT-COMPLETE', (string) $this->percentComplete));
        }

        if ($this->priority !== null) {
            $payload->property(TextProperty::create('PRIORITY', (string) $this->priority));
        }

        if ($this->organizer) {
            $payload->property(CalendarAddressProperty::create('ORGANIZER', $this->organizer));
        }

        foreach ($this->attendees as $attendee) {
            $payload->property(CalendarAddressProperty::create('ATTENDEE', $attendee));
        }

        if ($this->status) {
            $payload->property(TextProperty::createFromEnum('STATUS', $this->status));
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

        if ($this->duration !== null) {
            $payload->property(TextProperty::create('DURATION', $this->duration));
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
                is_array($attachment) && $attachment['type'] !== null => UriProperty::create('ATTACH', $attachment['url'])->addParameter(Parameter::create('FMTTYPE', $attachment['type'])),
                default => is_array($attachment) ? UriProperty::create('ATTACH', $attachment['url']) : UriProperty::create('ATTACH', (string)$attachment),
            };

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
            DateTimeProperty::fromDateTime($name, $value->getDateTime(), $value->hasTime(), $this->withTimezone)
        );

        return $this;
    }

}
