<?php

namespace Spatie\IcalendarGenerator\Enums;

enum TodoStatus: string
{
    case NeedsAction = 'NEEDS-ACTION';
    case Completed = 'COMPLETED';
    case InProcess = 'IN-PROCESS';
    case Cancelled = 'CANCELLED';
}
