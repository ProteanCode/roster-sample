<?php

namespace App\Classes\Parsers\CCNX\Factories;

use App\Classes\Parsers\CCNX\Strategies\FlightRecordStrategy;
use App\Classes\Parsers\CCNX\Strategies\IRosterStrategy;
use RuntimeException;

class ActivityStrategyFactory
{
    const ACTIVITY_COLUMN_NAME = 'Activity';

    public function __construct(private array $headers)
    {
    }

    public function resolve(array $values): IRosterStrategy
    {
        $activity = $this->getActivity($values);

        if (self::isFlightStrategy($activity)) {
            return new FlightRecordStrategy($values);
        }

        throw new \RuntimeException("Cannot resolve the correct strategy for record");
    }

    private function isFlightStrategy(string $activity): bool
    {
        $matches = [];

        preg_match('(^[A-Z]{2}\d+$)', $activity, $matches);

        return !empty($matches);
    }

    private function getActivity(array $values): string
    {
        return $values[$this->getActivityIndex()];
    }

    private function getActivityIndex(): int
    {
        $index = array_search(self::ACTIVITY_COLUMN_NAME, $this->headers);

        if (!$index) {
            throw new RuntimeException("Missing activity data");
        }

        return $index;
    }
}
