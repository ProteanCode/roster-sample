<?php

namespace App\Classes\Dtos;

use App\Classes\Enums\EventType;
use Carbon\Carbon;

class RosterFlightEvent extends RosterEvent
{
    public function __construct(
        public readonly string $number,
        public readonly string $from,
        public readonly string $to,
        public readonly Carbon $stdz,
        public readonly Carbon $staz
    )
    {

    }

    public function getType(): string
    {
        return EventType::Flight->value;
    }

    function getDate(): Carbon
    {
        return $this->stdz;
    }

    public function getLocation(): ?string
    {
        return $this->from;
    }

    public function getMetadata(): array
    {
        return array_merge(parent::getMetadata(), [
            'number' => $this->number,
            'from' => $this->from,
            'to' => $this->to,
            'stdz' => $this->stdz,
            'staz' => $this->staz,
        ]);
    }
}
