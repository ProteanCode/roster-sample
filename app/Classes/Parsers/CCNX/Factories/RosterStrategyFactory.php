<?php

namespace App\Classes\Parsers\CCNX\Factories;

use App\Classes\Parsers\CCNX\Strategies\IRosterStrategy;
use Carbon\Carbon;
use RuntimeException;

abstract class RosterStrategyFactory
{
    const ACTIVITY_COLUMN_NAME = 'Activity';
    const CHECK_IN_COLUMN_NAME = 'C/I(Z)';
    const CHECK_OUT_COLUMN_NAME = 'C/O(Z)';

    public function __construct(protected array $headers)
    {

    }

    abstract public function resolve(Carbon $currentDate, array $values): ?IRosterStrategy;

    protected function getColumnIndex(string $name): int
    {
        $index = array_search($name, $this->headers);

        if ($index === false) {
            throw new RuntimeException("Missing header column: " . $name);
        }

        return $index;
    }
}
