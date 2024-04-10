<?php

namespace App\Classes\Parsers\CCNX\Strategies;

use App\Classes\Dtos\RosterEvent;
use Carbon\Carbon;
use RuntimeException;

class StandbyRecordStrategy extends RecordStrategy implements IRosterStrategy
{
    public function __construct(
        private readonly Carbon $currentDate,
        private readonly array  $headers,
        private readonly array  $values)
    {

    }

    public function getEvent(): RosterEvent
    {
        throw new RuntimeException("Unimplemented " . __CLASS__);
    }
}
