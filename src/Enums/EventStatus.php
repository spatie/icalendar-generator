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
    protected static function values(): array
    {
        return [
            'tentative' => 'TENTATIVE',
            'confirmed' => 'CONFIRMED',
            'cancelled' => 'CANCELLED',
        ];
    }
}
