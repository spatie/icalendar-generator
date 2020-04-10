<?php

namespace Spatie\IcalendarGenerator\Enums;

use Spatie\Enum\Enum;

/**
 * @method static self needs_action()
 * @method static self accepted()
 * @method static self declined()
 * @method static self tentative()
 * @method static self delegated()
 */
class ParticipationStatus extends Enum
{
    const MAP_VALUE = [
        'needs_action' => 'NEEDS-ACTION',
        'accepted' => 'ACCEPTED',
        'declined' => 'DECLINED',
        'tentative' => 'TENTATIVE',
        'delegated' => 'DELEGATED',
    ];
}
