<?php

namespace Spatie\IcalendarGenerator\Enums;

enum EventStatus: string
{
    case Tentative = 'TENTATIVE';
    case Confirmed = 'CONFIRMED';
    case Cancelled = 'CANCELLED';
}
