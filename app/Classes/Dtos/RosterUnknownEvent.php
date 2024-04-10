<?php

namespace App\Classes\Dtos;

use App\Classes\Enums\EventType;
use Carbon\Carbon;

class RosterUnknownEvent extends RosterEvent
{
    public function __construct(
        public readonly Carbon $date
    )
    {

    }

    public function getType(): string
    {
        return EventType::Unknown->value;
    }

    function getDate(): Carbon
    {
        return $this->date;
    }
}
