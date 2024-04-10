<?php

namespace App\Classes\Parsers\CCNX\Strategies;

abstract class RecordStrategy
{
    protected function getCellValue(string $key, array $headers, array $values): string|int|float
    {
        $index = array_search($key, $headers);

        return $values[$index];
    }

    protected function getHourFromTimeCell(int $input): int
    {
        $as4 = str_pad($input, 4, '0', STR_PAD_LEFT);

        return (int)substr($as4, 0, 2);
    }

    protected function getMinuteFromTimeCell(int $input): int
    {
        $as4 = str_pad($input, 4, '0', STR_PAD_LEFT);

        return (int)substr($as4, 2, 2);
    }
}
