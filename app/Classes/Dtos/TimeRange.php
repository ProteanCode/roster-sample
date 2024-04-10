<?php

namespace App\Classes\Dtos;

use Carbon\Carbon;

class RosterCheckInEvent extends RosterEvent
{
    public function __construct(
        public Carbon $date
    )
    {
        $this->date = 1;
    }

    function getDate(): Carbon
    {
        return $this->date;
    }
}
