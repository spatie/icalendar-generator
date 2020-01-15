<?php

namespace Spatie\IcalendarGenerator\ValueObjects;

use Spatie\IcalendarGenerator\Enums\ParticipationStatus;

class CalendarAddress
{
    /** @var string */
    public $email;

    /** @var null|string */
    public $name = null;

    /** @var \Spatie\IcalendarGenerator\Enums\ParticipationStatus|null */
    public $participationStatus = null;

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
