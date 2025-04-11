<?php

namespace Spatie\IcalendarGenerator\Enums;

enum ParticipationStatus:string
{
    case NeedsAction = 'NEEDS-ACTION';
    case Accepted = 'ACCEPTED';
    case Declined = 'DECLINED';
    case Tentative = 'TENTATIVE';
    case Delegated = 'DELEGATED';
}
