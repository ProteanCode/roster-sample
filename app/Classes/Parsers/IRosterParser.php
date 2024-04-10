<?php

namespace App\Classes\Parsers;

use App\Classes\Collections\RosterEventCollection;

interface IRosterParser extends IParser
{
    public function parse(): RosterEventCollection;
}
