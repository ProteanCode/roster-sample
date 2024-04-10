<?php

namespace App\Classes\Dtos;

use App\Classes\Enums\EventType;
use Carbon\Carbon;

class RosterStandbyEvent extends RosterEvent
{
    public function __construct(
        public readonly Carbon $from,
        public readonly Carbon $to
    )
    {

    }

    public function getType(): string
    {
        return EventType::StandBy->value;
    }

    function getDate(): Carbon
    {
        return $this->from;
    }

    public function getMetadata(): array
    {
        return array_merge(parent::getMetadata(), [
            'from' => $this->from,
            'to' => $this->to,
        ]);
    }
}
