<?php

namespace App\Classes\Parsers\CCNX\Strategies;

use App\Classes\Dtos\RosterCheckInEvent;
use App\Classes\Dtos\RosterEvent;
use App\Classes\Dtos\RosterFlightEvent;
use Carbon\Carbon;

class FlightRecordStrategy extends RecordStrategy implements IRosterStrategy
{
    public function __construct(private array $headers, private array $values, private ?RosterCheckInEvent $currentCheckIn)
    {

    }

    public function getEvent(): RosterEvent
    {
        return new RosterFlightEvent(
            $this->getFrom(),
            $this->getTo(),
            $this->getStdz(),
            $this->getStaz()
        );
    }

    private function getFrom(): string
    {
        return $this->getCellValue('From', $this->headers, $this->values);
    }

    private function getTo(): string
    {
        return $this->getCellValue('To', $this->headers, $this->values);
    }

    private function getStdz(): Carbon
    {
        $cellValue = $this->getCellValue('STD(Z)', $this->headers, $this->values);

        return $this->currentCheckIn->getDate()->clone()
            ->setHour($this->getHourFromTimeCell($cellValue))
            ->setMinute($this->getMinuteFromTimeCell($cellValue))
            ->setSeconds(0);
    }

    private function getStaz(): Carbon
    {
        $cellValue = $this->getCellValue('STA(Z)', $this->headers, $this->values);

        return $this->currentCheckIn->getDate()->clone()
            ->setHour($this->getHourFromTimeCell($cellValue))
            ->setMinute($this->getMinuteFromTimeCell($cellValue))
            ->setSeconds(0);
    }
}
