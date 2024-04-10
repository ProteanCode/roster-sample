<?php

namespace App\Classes\Parsers\CCNX\Strategies;

use App\Classes\Dtos\RosterCheckInEvent;
use App\Classes\Dtos\RosterEvent;
use App\Classes\Parsers\CCNX\Factories\RosterStrategyFactory;
use Carbon\Carbon;

class CheckInRecordStrategy extends RecordStrategy implements IRosterStrategy
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
        return new RosterCheckInEvent(
            $this->getCiz()
        );
    }

    private function getCiz(): Carbon
    {
        $cellValue = $this->getCellValue(RosterStrategyFactory::CHECK_IN_COLUMN_NAME, $this->headers, $this->values);

        return $this->currentDate->clone()
            ->setTimezone('UTC')
            ->setHour($this->getHourFromTimeCell($cellValue))
            ->setMinute($this->getMinuteFromTimeCell($cellValue))
            ->setSeconds(0);
    }
}
