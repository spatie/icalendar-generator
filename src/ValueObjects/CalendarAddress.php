<?php

namespace Spatie\IcalendarGenerator\ValueObjects;

use Spatie\IcalendarGenerator\Enums\ParticipationStatus;

class CalendarAddress
{
    public function __construct(
        public string $email,
        public ?string $name = null,
        public ?ParticipationStatus $participationStatus = null,
        public bool $requiresResponse = false
    ) {
    }
}
