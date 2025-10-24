<?php

namespace Spatie\IcalendarGenerator\Components\Concerns;

use Spatie\IcalendarGenerator\ComponentPayload;
use Spatie\IcalendarGenerator\Enums\ParticipationStatus;
use Spatie\IcalendarGenerator\Properties\CalendarAddressProperty;
use Spatie\IcalendarGenerator\ValueObjects\CalendarAddress;

trait HasAttendees
{
    /** @var CalendarAddress[] */
    protected array $attendees = [];

    public function attendee(
        string $email,
        ?string $name = null,
        ?ParticipationStatus $participationStatus = null,
        bool $requiresResponse = false
    ): self {
        $this->attendees[] = new CalendarAddress($email, $name, $participationStatus, $requiresResponse);

        return $this;
    }

    protected function resolveAttendeeProperties(ComponentPayload $payload): self
    {
        foreach ($this->attendees as $attendee) {
            $payload->property(CalendarAddressProperty::create('ATTENDEE', $attendee));
        }

        return $this;
    }
}
