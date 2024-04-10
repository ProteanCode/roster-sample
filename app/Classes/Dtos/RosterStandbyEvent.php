<?php

namespace App\Classes\Dtos;

use Carbon\Carbon;

class RosterStandbyEvent extends RosterEvent
{
    public function __construct(
        public readonly Carbon $from,
        public readonly Carbon $to
    )
    {

    }

    function getDate(): Carbon
    {
        return $this->from;
    }
}
