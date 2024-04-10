<?php

namespace App\Classes\Parsers\CCNX\Factories;

use App\Classes\Parsers\CCNX\Strategies\CheckInRecordStrategy;
use App\Classes\Parsers\CCNX\Strategies\CheckOutRecordStrategy;
use App\Classes\Parsers\CCNX\Strategies\IRosterStrategy;
use App\Classes\Parsers\CCNX\Strategies\StandbyRecordStrategy;
use Carbon\Carbon;

class RosterCheckInOutStrategyFactory extends RosterStrategyFactory
{
    public function resolve(Carbon $currentDate, array $values): ?IRosterStrategy
    {
        if (self::isProhibitedActivity($values)) {
            return null;
        }

        if (self::isStandbyStrategy($values)) {
            return (new StandbyRecordStrategy($currentDate, $this->headers, $values));
        }

        if (self::isCheckInStrategy($values)) {
            return (new CheckInRecordStrategy($currentDate, $this->headers, $values));
        }

        if (self::isCheckOutStrategy($values)) {
            return (new CheckOutRecordStrategy($currentDate, $this->headers, $values));
        }

        return null;
    }

    private function isProhibitedActivity(array $values): bool
    {
        return in_array(
            $values[$this->getColumnIndex(self::ACTIVITY_COLUMN_NAME)],
            [
                "CAR"
            ],
            true
        );
    }

    private function isCheckInStrategy(array $values)
    {
        $hasCheckIn = !empty($values[$this->getColumnIndex(self::CHECK_IN_COLUMN_NAME)]);
        $hasCheckOut = !empty($values[$this->getColumnIndex(self::CHECK_OUT_COLUMN_NAME)]);

        return $hasCheckIn && !$hasCheckOut;
    }

    private function isCheckOutStrategy(array $values)
    {
        $hasCheckIn = !empty($values[$this->getColumnIndex(self::CHECK_IN_COLUMN_NAME)]);
        $hasCheckOut = !empty($values[$this->getColumnIndex(self::CHECK_OUT_COLUMN_NAME)]);

        return !$hasCheckIn && $hasCheckOut;
    }

    private function isStandbyStrategy(array $values)
    {
        $hasCheckIn = !empty($values[$this->getColumnIndex(self::CHECK_IN_COLUMN_NAME)]);
        $hasCheckOut = !empty($values[$this->getColumnIndex(self::CHECK_OUT_COLUMN_NAME)]);

        return $hasCheckIn && $hasCheckOut;
    }
}
