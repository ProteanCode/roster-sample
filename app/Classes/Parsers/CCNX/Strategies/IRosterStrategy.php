<?php

namespace App\Classes\Parsers\CCNX\Strategies;

use App\Classes\Dtos\RosterEvent;

interface IRosterStrategy
{
    public function getEvent(): RosterEvent;
}
