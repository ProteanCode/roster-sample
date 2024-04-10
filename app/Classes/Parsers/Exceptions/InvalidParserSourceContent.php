<?php

namespace App\Classes\Parsers\Exceptions;

use RuntimeException;
use Throwable;

class InvalidParserSourceContent extends RuntimeException
{
    public function __construct(string $parser, ?Throwable $previous = null)
    {
        parent::__construct('The provided source for parser ' . $parser . ' contains invalid data', 0, $previous);
    }
}
