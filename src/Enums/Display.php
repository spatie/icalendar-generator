<?php

namespace Spatie\IcalendarGenerator\Enums;

enum Display: string
{
    case Badge = 'BADGE';
    case Graphic = 'GRAPHIC';
    case Fullsize = 'FULLSIZE';
    case Thumbnail = 'THUMBNAIL';
}
