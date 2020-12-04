<?php

namespace Spatie\IcalendarGenerator\ValueObjects;

use Spatie\IcalendarGenerator\Enums\ParticipationStatus;

class CalendarAddress
{
    /** @var string */
    public string $email;

    public ?string $name = null;

    public ?ParticipationStatus $participationStatus = null;

    public function __construct(
        string $email,
        string $name = null,
        ParticipationStatus $participationStatus = null
    ) {
        $this->email = $email;
        $this->name = $name;
        $this->participationStatus = $participationStatus;
    }
}
