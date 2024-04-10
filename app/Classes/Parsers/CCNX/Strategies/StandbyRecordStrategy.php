<?php

namespace App\Classes\Parsers\CCNX\Strategies;

use App\Classes\Dtos\RosterEvent;
use App\Classes\Dtos\RosterStandbyEvent;
use App\Classes\Parsers\CCNX\Factories\RosterCheckInOutStrategyFactory;
use Carbon\Carbon;

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
        return (new RosterStandbyEvent(
            $this->getCiz(),
            $this->getCoz(),
        ));
    }

    private function getCiz(): Carbon
    {
        $cellValue = $this->getCellValue(RosterCheckInOutStrategyFactory::CHECK_IN_COLUMN_NAME, $this->headers, $this->values);

        return $this->currentDate->clone()
            ->setTimezone('UTC')
            ->setHour($this->getHourFromTimeCell($cellValue))
            ->setMinute($this->getMinuteFromTimeCell($cellValue))
            ->setSeconds(0);
    }

    private function getCoz(): Carbon
    {
        $cellValue = $this->getCellValue(RosterCheckInOutStrategyFactory::CHECK_OUT_COLUMN_NAME, $this->headers, $this->values);

        return $this->currentDate->clone()
            ->setTimezone('UTC')
            ->setHour($this->getHourFromTimeCell($cellValue))
            ->setMinute($this->getMinuteFromTimeCell($cellValue))
            ->setSeconds(0);
    }
}
