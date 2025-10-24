<?php

namespace Spatie\IcalendarGenerator\Components\Concerns;

use Spatie\IcalendarGenerator\ComponentPayload;
use Spatie\IcalendarGenerator\Properties\CalendarAddressProperty;
use Spatie\IcalendarGenerator\ValueObjects\CalendarAddress;

trait HasOrganizer
{
    protected ?CalendarAddress $organizer = null;

    public function organizer(string $email, ?string $name = null): self
    {
        $this->organizer = new CalendarAddress($email, $name);

        return $this;
    }

    protected function resolveOrganizerProperties(ComponentPayload $payload): self
    {
        if ($this->organizer) {
            $payload->property(CalendarAddressProperty::create('ORGANIZER', $this->organizer));
        }

        return $this;
    }
}
