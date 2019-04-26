<?php

namespace Spatie\IcalendarGenerator\Components;

use DateInterval;
use DateTimeInterface;
use Spatie\IcalendarGenerator\ComponentPayload;
use Spatie\IcalendarGenerator\PropertyTypes\Parameter;
use Spatie\IcalendarGenerator\PropertyTypes\DateTimePropertyType;

final class Alert extends Component
{
    /** @var \DateTimeInterface */
    private $triggerAt;

    /** @var null|string */
    private $message;

    public static function create(DateTimeInterface $triggerAt, string $description = null): Alert
    {
        return new self($triggerAt, $description);
    }

    public function __construct(DateTimeInterface $triggerAt, string $description = null)
    {
        $this->triggerAt = $triggerAt;
        $this->message = $description;
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

    public function message(string $message): Alert
    {
        $this->message = $message;

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
            ->textProperty('DESCRIPTION', $this->message)
            ->property(
                new DateTimePropertyType('TRIGGER', $this->triggerAt),
                [new Parameter('VALUE', 'DATE-TIME')]
            );
    }
}
