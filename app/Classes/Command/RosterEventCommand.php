<?php

namespace App\Classes\Command;

use App\Classes\Collections\RosterEventCollection;
use App\Classes\Dtos\RosterEvent;
use App\Models\RosterEvent as RosterEventModel;
use Illuminate\Support\Facades\DB;

class RosterEventCommand
{
    public function storeCollection(RosterEventCollection $collection): bool
    {
        return DB::transaction(function () use ($collection) {
            $collection->each(function (RosterEvent $rosterEvent) {
                RosterEventModel::updateOrCreate([
                    'type' => $rosterEvent->getType(),
                    'date' => $rosterEvent->getDate(),
                    'location' => $rosterEvent->getLocation()
                ], [
                    'type' => $rosterEvent->getType(),
                    'date' => $rosterEvent->getDate(),
                    'location' => $rosterEvent->getLocation(),
                    'metadata' => $rosterEvent->getMetadata()
                ]);
            });

            return true;
        });
    }
}
