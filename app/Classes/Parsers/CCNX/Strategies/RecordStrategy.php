<?php

namespace App\Classes\Parsers\CCNX\Strategies;

use App\Classes\Dtos\RosterEvent;
use App\Classes\Dtos\RosterFlightEvent;

class FlightRecordStrategy implements IRosterStrategy
{
    public function __construct(private array $values)
    {

    }

    public function getEvent(): RosterEvent
    {
        return new RosterFlightEvent(

        );
    }

    private function getFrom(): string {
        
    }
}
