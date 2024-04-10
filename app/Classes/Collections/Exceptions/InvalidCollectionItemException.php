<?php

namespace App\Classes\Collections\Exceptions;

use RuntimeException;
use Throwable;

class InvalidCollectionItemException extends RuntimeException
{
    public function __construct(mixed $item, string $expectedType, ?Throwable $previous = null)
    {
        $itemName = $item;

        if (is_object($item)) {
            $itemName = $item::class;
        }

        parent::__construct('The provided item ' . $itemName . ' is not valid for collection type ' . $expectedType, 0, $previous);
    }
}
