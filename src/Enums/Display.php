<?php

namespace Spatie\IcalendarGenerator\Enums;

use Spatie\Enum\Enum;

/**
 * @method static self badge()
 * @method static self graphic()
 * @method static self fullsize()
 * @method static self thumbnail()
 */
class Display extends Enum
{
    protected static function values(): array
    {
        return [
            'badge' => 'BADGE',
            'graphic' => 'GRAPHIC',
            'fullsize' => 'FULLSIZE',
            'thumbnail' => 'THUMBNAIL',
        ];
    }
}
