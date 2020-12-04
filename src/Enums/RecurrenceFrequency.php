<?php

namespace Spatie\IcalendarGenerator\Enums;

use Spatie\Enum\Enum;

/**
 * @method static self yearly()
 * @method static self monthly()
 * @method static self weekly()
 * @method static self daily()
 * @method static self hourly()
 * @method static self minutely()
 * @method static self secondly()
 */
class RecurrenceFrequency extends Enum
{
    protected static function values(): array
    {
        return [
            'yearly' => 'YEARLY',
            'monthly' => 'MONTHLY',
            'weekly' => 'WEEKLY',
            'daily' => 'DAILY',
            'hourly' => 'HOURLY',
            'minutely' => 'MINUTELY',
            'secondly' => 'SECONDLY',
        ];
    }
}
