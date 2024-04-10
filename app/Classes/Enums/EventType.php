<?php

namespace App\Classes\Enums;

enum EventType: string
{
    case CheckIn = 'check-in';
    case CheckOut = 'check-out';
    case DayOff = 'day-off';
    case Flight = 'flight';
    case StandBy = 'standby';
    case Unknown = 'unknown';
}
