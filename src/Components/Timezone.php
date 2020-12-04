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
    private string $identifier;

    private ?DateTimeValue $lastModified = null;

    private ?string $url = null;

    /** @var \Spatie\IcalendarGenerator\Components\TimezoneEntry[] */
    private array $entries = [];

    public static function create(string $identifier): self
    {
        return new self($identifier);
    }

    public function __construct(string $identifier)
    {
        $this->identifier = $identifier;
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
     * @param $entry \Spatie\IcalendarGenerator\Components\TimezoneEntry|array
     *
     * @return \Spatie\IcalendarGenerator\Components\Timezone
     */
    public function entry($entry): Timezone
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
        return ComponentPayload::create($this->getComponentType())
            ->property(TextProperty::create('TZID', $this->identifier))
            ->optional(
                $this->url,
                fn () => TextProperty::create('TZURL', $this->url)->withoutEscaping()
            )
            ->optional(
                $this->lastModified,
                fn () => DateTimeProperty::create('LAST-MODIFIED', $this->lastModified)
            )
            ->subComponent(...$this->entries);
    }
}
