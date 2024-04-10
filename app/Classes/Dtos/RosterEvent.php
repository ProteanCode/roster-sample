<?php

namespace App\Classes\Dtos;

use Carbon\Carbon;

abstract class RosterEvent
{
    public ?string $location = null;

    abstract function getDate(): Carbon;
    abstract function getType(): string;

    public function getMetadata(): array
    {
        return [];
    }

    public function setLocation(?string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }
}
