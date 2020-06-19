<?php

namespace Spatie\IcalendarGenerator\Enums;

use Spatie\Enum\Enum;

/**
 * @method static self monday()
 * @method static self tuesday()
 * @method static self wednesday()
 * @method static self thursday()
 * @method static self friday()
 * @method static self saturday()
 * @method static self sunday()
 */
class RecurrenceDay extends Enum
{
    protected static function values(): array
    {
        return [
            'monday' => 'MO',
            'tuesday' => 'TU',
            'wednesday' => 'WE',
            'thursday' => 'TH',
            'friday' => 'FR',
            'saturday' => 'SA',
            'sunday' => 'SU',
        ];
    }
}
