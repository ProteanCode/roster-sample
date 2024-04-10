<?php

namespace App\Classes\Parsers\CCNX\Strategies;

use App\Classes\Dtos\RosterCheckInEvent;
use App\Classes\Dtos\RosterCheckOutEvent;
use App\Classes\Dtos\RosterEvent;
use App\Classes\Parsers\CCNX\Factories\RosterCheckInOutStrategyFactory;
use Carbon\Carbon;

class CheckOutRecordStrategy extends RecordStrategy implements IRosterStrategy
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
        return new RosterCheckOutEvent(
            $this->getCoz()
        );
    }

    private function getCoz(): Carbon
    {
        $cellValue = $this->getCellValue(RosterCheckInOutStrategyFactory::CHECK_OUT_COLUMN_NAME, $this->headers, $this->values);

        return $this->currentDate->clone()
            ->setHour($this->getHourFromTimeCell($cellValue))
            ->setMinute($this->getMinuteFromTimeCell($cellValue))
            ->setSeconds(0);
    }
}
