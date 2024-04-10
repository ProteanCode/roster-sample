<?php

namespace App\Classes\Dtos;

use App\Classes\Enums\RosterActivity;

class RosterFlightEvent
{
    public function __construct(
        public string $flightNo
    )
    {

    }
}
