<?php

namespace Spatie\IcalendarGenerator\Enums;

enum Classification: string
{
    case Public = 'PUBLIC';
    case Private = 'PRIVATE';
    case Confidential = 'CONFIDENTIAL';
}
