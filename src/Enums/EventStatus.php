<?php

namespace Spatie\IcalendarGenerator\Enums;

use Spatie\Enum\Enum;

/**
 * @method static self accepted()
 * @method static self declined()
 * @method static self tentative()
 */
class EventStatus extends Enum
{
    const MAP_VALUE = [
        'accepted' => 'ACCEPTED',
        'declined' => 'DECLINED',
        'tentative' => 'TENTATIVE',
    ];
}
