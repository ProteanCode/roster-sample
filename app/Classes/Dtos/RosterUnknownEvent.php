<?php

namespace App\Classes\Dtos;

use Carbon\Carbon;

class RosterUnknownEvent extends RosterEvent
{
    public function __construct(
        public readonly Carbon $date
    )
    {

    }

    function getDate(): Carbon
    {
        return $this->date;
    }
}
