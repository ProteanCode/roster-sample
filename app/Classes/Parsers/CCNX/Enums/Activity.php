<?php

namespace App\Classes\Parsers\CCNX\Enums;

enum Activity: string
{
    case OFF = 'Day Off';
    case SBY = 'Standby';
    case FLT = 'Flight';
    case CI = 'Check-in';
    case CO = 'Check-out';
    case UNK = 'Unknown';
}
