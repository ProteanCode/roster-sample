<?php

namespace App\Classes\Dtos;

use Carbon\Carbon;

class RosterFlightEvent extends RosterEvent
{
    public function __construct(
        public readonly string $number,
        public readonly string $from,
        public readonly string $to,
        public readonly Carbon $stdz,
        public readonly Carbon $staz
    )
    {

    }

    function getDate(): Carbon
    {
        return $this->stdz;
    }
}
