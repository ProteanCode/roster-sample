<?php

namespace App\Classes\Parsers\CCNX\Strategies;

use App\Classes\Dtos\RosterCheckInEvent;
use App\Classes\Dtos\RosterEvent;
use Carbon\Carbon;

class CheckOutRecordStrategy extends RecordStrategy implements IRosterStrategy
{
    public function __construct(
        private readonly array  $headers,
        private readonly array  $values,
        private readonly Carbon $currentDate)
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
        $cellValue = $this->getCellValue('C/I(Z)', $this->headers, $this->values);

        return $this->currentDate->clone()
            ->setHour($this->getHourFromTimeCell($cellValue))
            ->setMinute($this->getMinuteFromTimeCell($cellValue))
            ->setSeconds(0);
    }
}
