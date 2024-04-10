<?php

namespace App\Classes\Dtos;

use App\Classes\Enums\EventType;
use Carbon\Carbon;

class RosterCheckOutEvent extends RosterEvent
{
    public ?string $location = null;

    public function __construct(
        public readonly Carbon $date
    )
    {

    }

    public function getType(): string
    {
        return EventType::CheckOut->value;
    }

    public function setLocation(?string $location): self
    {
        $this->location = $location;

        return $this;
    }

    function getDate(): Carbon
    {
        return $this->date;
    }
}
