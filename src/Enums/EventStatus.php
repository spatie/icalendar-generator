<?php

namespace Spatie\IcalendarGenerator\Enums;

use Spatie\Enum\Enum;

/**
 * @method static self tentative()
 * @method static self confirmed()
 * @method static self cancelled()
 */
class EventStatus extends Enum
{
    const MAP_VALUE = [
        'tentative' => 'TENTATIVE',
        'confirmed' => 'CONFIRMED',
        'cancelled' => 'CANCELLED',
    ];
}
