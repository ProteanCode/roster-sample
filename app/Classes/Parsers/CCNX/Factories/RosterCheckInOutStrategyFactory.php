<?php

namespace App\Classes\Parsers\CCNX\Factories;

use App\Classes\Dtos\RosterCheckInEvent;
use App\Classes\Parsers\CCNX\Strategies\CheckInRecordStrategy;
use App\Classes\Parsers\CCNX\Strategies\CheckOutRecordStrategy;
use App\Classes\Parsers\CCNX\Strategies\FlightRecordStrategy;
use App\Classes\Parsers\CCNX\Strategies\IRosterStrategy;
use Carbon\Carbon;
use RuntimeException;

class RosterActivityStrategyFactory
{
    const ACTIVITY_COLUMN_NAME = 'Activity';
    const CHECK_IN_COLUMN_NAME = 'C/I(Z)';
    const CHECK_OUT_COLUMN_NAME = 'C/O(Z)';

    public function __construct(private array $headers)
    {

    }

    public function resolve(Carbon $currentDate, ?RosterCheckInEvent $currentCheckIn, array $values): IRosterStrategy
    {
        if (self::isCheckInStrategy($values)) {
            return (new CheckInRecordStrategy($currentDate, $this->headers, $values));
        }

        if (self::isCheckOutStrategy($values)) {
            return (new CheckOutRecordStrategy($currentDate, $this->headers, $values));
        }

        if (self::isFlightStrategy($values)) {
            return (new FlightRecordStrategy($this->headers, $values, $currentCheckIn));
        }

        throw new RuntimeException("Cannot resolve the correct strategy for record");
    }

    private function isCheckInStrategy(array $values): bool
    {
        return !empty($values[$this->getColumnIndex(self::CHECK_IN_COLUMN_NAME)]);
    }

    private function isCheckOutStrategy(array $values): bool
    {
        return !empty($values[$this->getColumnIndex(self::CHECK_OUT_COLUMN_NAME)]);
    }

    private function isFlightStrategy(array $values): bool
    {
        $activity = $values[$this->getColumnIndex(self::ACTIVITY_COLUMN_NAME)];

        $matches = [];

        preg_match('(^[A-Z]{2}\d+$)', $activity, $matches);

        return !empty($matches);
    }

    private function getColumnIndex(string $name): int
    {
        $index = array_search($name, $this->headers);

        if ($index === false) {
            throw new RuntimeException("Missing header column: " . $name);
        }

        return $index;
    }
}
