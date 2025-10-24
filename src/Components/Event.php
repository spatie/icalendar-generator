<?php

namespace Spatie\IcalendarGenerator\Components;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use LogicException;
use Spatie\IcalendarGenerator\ComponentPayload;
use Spatie\IcalendarGenerator\Components\Concerns\HasAlerts;
use Spatie\IcalendarGenerator\Components\Concerns\HasAttachments;
use Spatie\IcalendarGenerator\Components\Concerns\HasAttendees;
use Spatie\IcalendarGenerator\Components\Concerns\HasLocation;
use Spatie\IcalendarGenerator\Components\Concerns\HasOrganizer;
use Spatie\IcalendarGenerator\Components\Concerns\HasRRule;
use Spatie\IcalendarGenerator\Enums\Classification;
use Spatie\IcalendarGenerator\Enums\Display;
use Spatie\IcalendarGenerator\Enums\EventStatus;
use Spatie\IcalendarGenerator\Properties\DateTimeProperty;
use Spatie\IcalendarGenerator\Properties\Parameter;
use Spatie\IcalendarGenerator\Properties\TextProperty;
use Spatie\IcalendarGenerator\Properties\UriProperty;
use Spatie\IcalendarGenerator\Timezones\HasTimezones;
use Spatie\IcalendarGenerator\Timezones\TimezoneRangeCollection;
use Spatie\IcalendarGenerator\ValueObjects\DateTimeValue;

class Event extends Component implements HasTimezones
{
    use HasAlerts;
    use HasAttachments;
    use HasAttendees;
    use HasLocation;
    use HasOrganizer;
    use HasRRule;

    protected ?DateTimeValue $starts = null;

    protected ?DateTimeValue $ends = null;

    protected DateTimeValue $created;

    protected ?string $description = null;

    protected ?string $googleConference = null;

    protected ?string $microsoftTeams = null;

    protected string $uuid;

    protected bool $withTimezone = true;

    protected bool $isFullDay = false;

    protected ?Classification $classification = null;

    protected ?bool $transparent = null;

    protected ?EventStatus $status = null;

    protected ?string $url = null;

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
            ->convertToTimezone(new DateTimeZone('UTC'));
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
            ->add(...$this->getRRuleTimezoneEntries());
    }

    protected function payload(): ComponentPayload
    {
        $payload = ComponentPayload::create($this->getComponentType());

        $this
            ->resolveProperties($payload)
            ->resolveLocationProperties($payload)
            ->resolveAttendeeProperties($payload)
            ->resolveAttachmentProperties($payload)
            ->resolveRruleProperties($payload)
            ->resolveOrganizerProperties($payload)
            ->resolveAlerts($payload);

        return $payload;
    }

    protected function resolveProperties(ComponentPayload $payload): self
    {
        $payload
            ->property(TextProperty::create('UID', $this->uuid))
            ->property(DateTimeProperty::create('DTSTAMP', $this->created));

        if ($this->starts) {
            $payload->property(
                DateTimeProperty::fromDateTime('DTSTART', $this->starts->getDateTime(), ! $this->isFullDay, $this->withTimezone)
            );
        }

        if ($end = $this->resolveEnd()) {
            $payload->property(
                DateTimeProperty::fromDateTime('DTEND', $end->getDateTime(), ! $this->isFullDay, $this->withTimezone)
            );
        }

        if ($this->name) {
            $payload->property(TextProperty::create('SUMMARY', $this->name));
        }

        if ($this->description) {
            $payload->property(TextProperty::create('DESCRIPTION', $this->description));
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

        if ($this->url) {
            $payload->property(UriProperty::create('URL', $this->url));
        }

        if ($this->sequence) {
            $payload->property(TextProperty::create('SEQUENCE', (string) $this->sequence));
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

    protected function resolveEnd(): DateTimeValue|null
    {
        if ($this->ends === null || $this->isFullDay === false) {
            return $this->ends;
        }

        $datetime = $this->ends->getDateTime();

        if (method_exists($datetime, 'modify')) {
            $datetime = $datetime->modify('+1 day');
        } else {
            throw new LogicException('The provided DateTimeInterface instance does not support the modify method.');
        }

        return DateTimeValue::create($datetime, false);
    }
}
