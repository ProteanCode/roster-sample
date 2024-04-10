<?php

namespace App\Http\Controllers;

use App\Classes\Command\RosterEventCommand;
use App\Classes\Parsers\IRosterParser;
use App\Http\Requests\Events\IndexEventsRequest;
use App\Http\Requests\Events\StoreEventsRequest;
use App\Models\RosterEvent;

class EventsController extends Controller
{
    public function index(IndexEventsRequest $request)
    {
        $data = RosterEvent::between($request->from, $request->to)
            ->ofType($request->type)
            ->forNextWeek($request->forNextWeek)
            ->forLocation($request->location)
            ->get();

        return response()->json($data);
    }

    public function store(StoreEventsRequest $request, IRosterParser $parser, RosterEventCommand $command)
    {
        $result = $command->storeCollection($parser->parse());

        return response()->json(null, $result ? 201 : 400);
    }
}
