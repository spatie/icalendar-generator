<?php

namespace Spatie\IcalendarGenerator\Components;

use DateTimeInterface;
use DateTimeZone;
use Spatie\IcalendarGenerator\ComponentPayload;
use Spatie\IcalendarGenerator\Properties\DateTimeProperty;
use Spatie\IcalendarGenerator\Properties\TextProperty;
use Spatie\IcalendarGenerator\ValueObjects\DateTimeValue;

class Timezone extends Component
{
    protected ?DateTimeValue $lastModified = null;

    public static function create(string $identifier): self
    {
        return new self($identifier);
    }

    /**
     * @param TimezoneEntry[] $entries
     */
    public function __construct(
        protected string $identifier,
        protected ?string $url = null,
        ?DateTimeInterface $lastModified = null,
        protected array $entries = [],
    ) {
        if ($lastModified) {
            $this->lastModified($lastModified);
        }
    }

    public function lastModified(DateTimeInterface $lastModified): self
    {
        $this->lastModified = DateTimeValue::create($lastModified)
            ->convertToTimezone(new DateTimeZone('UTC'));

        return $this;
    }

    public function url(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @param TimezoneEntry|array<TimezoneEntry>|null $entry
     */
    public function entry(TimezoneEntry|array|null $entry): Timezone
    {
        if (is_null($entry)) {
            return $this;
        }

        $this->entries = array_merge(
            $this->entries,
            is_array($entry) ? $entry : [$entry]
        );

        return $this;
    }

    public function getComponentType(): string
    {
        return 'VTIMEZONE';
    }

    public function getRequiredProperties(): array
    {
        return [
            'TZID',
        ];
    }

    protected function payload(): ComponentPayload
    {
        $payload = ComponentPayload::create($this->getComponentType())
            ->property(TextProperty::create('TZID', $this->identifier));

        if ($this->url) {
            $payload->property(TextProperty::create('TZURL', $this->url));
        }

        if ($this->lastModified) {
            $payload->property(DateTimeProperty::create('LAST-MODIFIED', $this->lastModified));
        }

        return $payload->subComponent(...$this->entries);
    }
}
