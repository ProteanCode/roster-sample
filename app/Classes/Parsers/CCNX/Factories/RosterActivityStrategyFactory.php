<?php

namespace App\Classes\Parsers\CCNX\Factories;

use App\Classes\Parsers\CCNX\Enums\Activity;
use App\Classes\Parsers\CCNX\Strategies\DayOffRecordStrategy;
use App\Classes\Parsers\CCNX\Strategies\FlightRecordStrategy;
use App\Classes\Parsers\CCNX\Strategies\IRosterStrategy;
use Carbon\Carbon;

class RosterActivityStrategyFactory extends RosterStrategyFactory
{
    public function resolve(Carbon $currentDate, array $values): ?IRosterStrategy
    {
        if (self::isFlightStrategy($values)) {
            return (new FlightRecordStrategy($currentDate, $this->headers, $values));
        }

        if (self::isDayOffStrategy($values)) {
            return (new DayOffRecordStrategy($currentDate, $this->headers, $values));
        }

        return null;
    }

    private function isFlightStrategy(array $values): bool
    {
        $activity = $values[$this->getColumnIndex(self::ACTIVITY_COLUMN_NAME)];

        $matches = [];

        preg_match('(^[A-Z]{2}\d+$)', $activity, $matches);

        return !empty($matches);
    }

    private function isDayOffStrategy(array $values): bool
    {
        return $values[$this->getColumnIndex(self::ACTIVITY_COLUMN_NAME)] === Activity::OFF->name;
    }
}
