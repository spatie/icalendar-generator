<?php

namespace Spatie\IcalendarGenerator\ValueObjects;

use Spatie\IcalendarGenerator\Enums\ParticipationStatus;

class CalendarAddress
{
    /** @var string */
    public string $email;

    public ?string $name = null;

    public bool $requiresResponse = false;

    public ?ParticipationStatus $participationStatus = null;

    public function __construct(
        string $email,
        string $name = null,
        ParticipationStatus $participationStatus = null,
        bool $requiresResponse = false
    ) {
        $this->email = $email;
        $this->name = $name;
        $this->participationStatus = $participationStatus;
        $this->requiresResponse = $requiresResponse;
    }
}
