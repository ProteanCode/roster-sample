<?php

namespace App\Classes\Strategies;

use App\Classes\Dtos\RosterEvent;

interface IRosterStrategy
{
    public function getEvent(): RosterEvent;
}
