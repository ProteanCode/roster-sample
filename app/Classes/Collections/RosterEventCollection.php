<?php

namespace App\Classes\Collections;

use App\Classes\Dtos\RosterItem;

class RosterCollection extends TypedCollection
{
    public function __construct($items = [])
    {
        parent::__construct($items, RosterItem::class);
    }
}
