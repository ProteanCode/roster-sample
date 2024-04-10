<?php

namespace App\Classes\Dtos;

use Carbon\Carbon;

abstract class RosterEvent
{
    abstract function getDate(): Carbon;
}
