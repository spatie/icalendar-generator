<?php

namespace Spatie\IcalendarGenerator\Enums;

enum RecurrenceFrequency:string
{
    case Yearly = 'YEARLY';
    case Monthly = 'MONTHLY';
    case Weekly = 'WEEKLY';
    case Daily = 'DAILY';
    case Hourly = 'HOURLY';
    case Minutely = 'MINUTELY';
    case Secondly = 'SECONDLY';
}
