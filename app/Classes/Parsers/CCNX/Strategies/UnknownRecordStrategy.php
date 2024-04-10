<?php

namespace App\Classes\Parsers\CCNX\Strategies;

use App\Classes\Dtos\RosterEvent;
use App\Classes\Dtos\RosterUnknownEvent;
use Carbon\Carbon;

class UnknownRecordStrategy extends RecordStrategy implements IRosterStrategy
{
    public function __construct(
        private readonly Carbon $currentDate,
        private readonly array  $headers,
        private readonly array  $values)
    {

    }

    public function getEvent(): RosterEvent
    {
        return (new RosterUnknownEvent(
            $this->currentDate
        ))->setLocation($this->getLocation());
    }

    private function getLocation(): string
    {
        return $this->getCellValue('From', $this->headers, $this->values);
    }
}
