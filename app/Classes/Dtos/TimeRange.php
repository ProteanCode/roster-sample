<?php

namespace App\Classes\Dtos;

use Carbon\Carbon;

class TimeRange
{
    public function __construct(
        public readonly Carbon $from,
        public readonly Carbon $to
    )
    {
    }
}
