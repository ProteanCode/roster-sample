<?php

namespace App\Classes\Parsers\CCNX\Strategies;

use App\Classes\Dtos\RosterDayOffEvent;
use App\Classes\Dtos\RosterEvent;
use Carbon\Carbon;

class DayOffRecordStrategy extends RecordStrategy implements IRosterStrategy
{
    public function __construct(
        private readonly Carbon $currentDate,
        private readonly array  $headers,
        private readonly array  $values
    )
    {

    }

    public function getEvent(): RosterEvent
    {
        return (new RosterDayOffEvent(
            $this->currentDate->clone()
                ->setTime(0,0,0,0)
                ->setTimezone('UTC')
        ));
    }
}
