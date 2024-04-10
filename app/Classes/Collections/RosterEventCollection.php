<?php

namespace App\Classes\Collections;

use App\Classes\Dtos\RosterEvent;

class RosterEventCollection extends TypedCollection
{
    public function __construct($items = [])
    {
        parent::__construct($items, RosterEvent::class);
    }

    public function getNearestPreviousEventOfType(int $currentIndex, string $eventClass): ?RosterEvent
    {
        if ($this->count() <= 1 || ($currentIndex - 1 < 0)) {
            return null;
        }

        for ($i = $currentIndex; $i >= 0; --$i) {
            $currentItem = $this->get($i);

            if (get_class($currentItem) === $eventClass) {
                return $currentItem;
            }
        }

        return null;
    }

    public function getNearestNextEventOfType(int $currentIndex, string $eventClass): ?RosterEvent
    {
        if ($this->count() <= 1 || ($currentIndex + 1 >= $this->count())) {
            return null;
        }

        for ($i = $currentIndex; $i < $this->count(); ++$i) {
            $currentItem = $this->get($i);

            if (get_class($currentItem) === $eventClass) {
                return $currentItem;
            }
        }

        return null;
    }
}
