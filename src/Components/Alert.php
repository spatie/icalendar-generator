<?php

namespace Spatie\Calendar\Components;

use DateInterval;
use DateTimeInterface;
use Spatie\Calendar\ComponentPayload;
use Spatie\Calendar\PropertyTypes\DateTimePropertyType;
use Spatie\Calendar\PropertyTypes\Parameter;

final class Alert extends Component
{
    /** @var \DateTimeInterface */
    protected $triggerAt;

    /** @var null|string */
    private $description;

    public static function create(DateTimeInterface $triggerAt, string $description = null): Alert
    {
        return new self($triggerAt, $description);
    }

    public function __construct(DateTimeInterface $triggerAt, string $description = null)
    {
        $this->triggerAt = $triggerAt;
        $this->description = $description;
    }

    public function getComponentType(): string
    {
        return 'ALARM';
    }

    public function getRequiredProperties(): array
    {
        return [
            'ACTION',
            'TRIGGER',
            'DESCRIPTION',
        ];
    }

    public function description(string $description): Alert
    {
        $this->description = $description;

        return $this;
    }

    public function trigger(DateInterval $triggerAt): Alert
    {
        $this->triggerAt = $triggerAt;

        return $this;
    }

    public function getPayload(): ComponentPayload
    {
        return ComponentPayload::create($this->getComponentType())
            ->textProperty('ACTION', 'DISPLAY')
            ->textProperty('DESCRIPTION', $this->description)
            ->property(
                new DateTimePropertyType('TRIGGER', $this->triggerAt),
                [new Parameter('VALUE', 'DATE-TIME')]
            );
    }
}
