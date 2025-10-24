<?php

namespace Spatie\IcalendarGenerator\Components;

use DateInterval;
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
use Spatie\IcalendarGenerator\Enums\TodoStatus;
use Spatie\IcalendarGenerator\Properties\DateTimeProperty;
use Spatie\IcalendarGenerator\Properties\DurationProperty;
use Spatie\IcalendarGenerator\Properties\TextProperty;
use Spatie\IcalendarGenerator\Properties\UriProperty;
use Spatie\IcalendarGenerator\Timezones\HasTimezones;
use Spatie\IcalendarGenerator\Timezones\TimezoneRangeCollection;
use Spatie\IcalendarGenerator\ValueObjects\DateTimeValue;

class Todo extends Component implements HasTimezones
{
    use HasAlerts;
    use HasAttachments;
    use HasAttendees;
    use HasOrganizer;
    use HasRRule;
    use HasLocation;

    protected string $uuid;

    protected DateTimeValue $created;

    protected ?string $name = null;

    protected ?string $description = null;

    protected ?DateTimeValue $starts = null;

    protected ?DateTimeValue $due = null;

    protected ?DateInterval $duration = null;

    protected ?Classification $classification = null;

    protected ?DateTimeValue $completed = null;

    protected ?int $percentCompleted = null;

    protected ?int $priority = null;

    protected ?TodoStatus $status = null;

    protected ?string $url = null;

    protected bool $withTimezone = true;

    protected ?int $sequence = null;

    public static function create(?string $name = null): Todo
    {
        return new self($name);
    }

    public function __construct(?string $name = null)
    {
        $this->name = $name;
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

    public function name(string $name): self
    {
        $this->name = $name;

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

    public function startsAt(DateTimeInterface $starts, bool $withTime = true): self
    {
        $this->starts = DateTimeValue::create($starts, $withTime);

        return $this;
    }

    public function duration(DateInterval $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function dueAt(DateTimeInterface $due, bool $withTime = true): self
    {
        $this->due = DateTimeValue::create($due, $withTime);

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

    /**
     * @param int<0,100> $percent
     */
    public function percentCompleted(int $percent): self
    {
        $this->percentCompleted = $percent;

        return $this;
    }

    /**
     * @param int<0, 9> $priority
     */
    public function priority(int $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    public function status(TodoStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function url(string $url): self
    {
        $this->url = $url;

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
            ->add($this->starts)
            ->add($this->due)
            ->add($this->created)
            ->add($this->completed)
            ->add(...$this->getRRuleTimezoneEntries());
    }

    protected function payload(): ComponentPayload
    {
        $payload = ComponentPayload::create($this->getComponentType());

        $this
            ->resolveProperties($payload)
            ->resolveAlerts($payload)
            ->resolveAttendeeProperties($payload)
            ->resolveAttachmentProperties($payload)
            ->resolveRruleProperties($payload)
            ->resolveOrganizerProperties($payload)
            ->resolveLocationProperties($payload);

        return $payload;
    }

    protected function resolveProperties(ComponentPayload $payload): self
    {
        $this->validate();

        $payload
            ->property(TextProperty::create('UID', $this->uuid))
            ->property(DateTimeProperty::create('DTSTAMP', $this->created));

        if ($this->starts) {
            $payload->property(
                DateTimeProperty::fromDateTime('DTSTART', $this->starts->getDateTime(), $this->starts->hasTime(), $this->withTimezone)
            );
        }

        if ($this->due) {
            $payload->property(
                DateTimeProperty::fromDateTime('DUE', $this->due->getDateTime(), $this->due->hasTime(), $this->withTimezone)
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

        if ($this->completed) {
            $payload->property(DateTimeProperty::fromDateTime('COMPLETED', $this->completed->getDateTime(), true, $this->withTimezone));
        }

        if ($this->percentCompleted !== null) {
            $payload->property(TextProperty::create('PERCENT-COMPLETE', (string) $this->percentCompleted));
        }

        if ($this->priority !== null) {
            $payload->property(TextProperty::create('PRIORITY', (string) $this->priority));
        }

        if ($this->status) {
            $payload->property(TextProperty::createFromEnum('STATUS', $this->status));
        }

        if ($this->url) {
            $payload->property(UriProperty::create('URL', $this->url));
        }

        if ($this->sequence) {
            $payload->property(TextProperty::create('SEQUENCE', (string) $this->sequence));
        }

        if ($this->duration !== null) {
            $payload->property(DurationProperty::create('DURATION', $this->duration));
        }

        return $this;
    }

    protected function validate(): void
    {
        if ($this->duration && $this->due) {
            throw new LogicException('Cannot set both due and duration on a todo');
        }

        if ($this->duration && $this->starts === null) {
            throw new LogicException('Cannot set duration on a todo without setting a start date');
        }
    }

}
