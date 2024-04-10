<?php

namespace App\Classes\Dtos;

use App\Classes\Enums\RosterActivity;

class RosterItem
{
    public function __construct(
        public RosterActivity $activity
    )
    {

    }
}
