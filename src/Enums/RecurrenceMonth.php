<?php

namespace Spatie\IcalendarGenerator\Enums;

use Exception;
use Spatie\Enum\Enum;

/**
 * @method static self january()
 * @method static self february()
 * @method static self march()
 * @method static self april()
 * @method static self may()
 * @method static self june()
 * @method static self july()
 * @method static self august()
 * @method static self september()
 * @method static self october()
 * @method static self november()
 * @method static self december()
 */
class RecurrenceMonth extends Enum
{
    protected static function values(): array
    {
        return [
            'january' => 1,
            'february' => 2,
            'march' => 3,
            'april' => 4,
            'may' => 5,
            'june' => 6,
            'july' => 7,
            'august' => 8,
            'september' => 9,
            'october' => 10,
            'november' => 11,
            'december' => 12,
        ];
    }

    public static function fromInt(int $month): self
    {
        if ($month < 1 || $month > 12) {
            throw new Exception('Months can only be between 1 and 12');
        }

        return new self($month);
    }
}
