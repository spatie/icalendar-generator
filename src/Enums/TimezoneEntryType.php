<?php

namespace Spatie\IcalendarGenerator\Enums;

use Spatie\Enum\Enum;

/**
 * @method static self standard()
 * @method static self daylight()
 */
class TimezoneEntryType extends Enum
{
    protected static function values(): array
    {
        return [
            'standard' => 'STANDARD',
            'daylight' => 'DAYLIGHT',
        ];
    }
}
