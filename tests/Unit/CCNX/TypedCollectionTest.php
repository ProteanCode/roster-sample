<?php

namespace Tests\Unit\CCNX;

use App\Classes\Collections\Exceptions\InvalidCollectionItemException;
use App\Classes\Collections\RosterEventCollection;
use App\Classes\Dtos\RosterCheckInEvent;
use App\Classes\Dtos\RosterCheckOutEvent;
use App\Classes\Dtos\RosterFlightEvent;
use App\Classes\Dtos\RosterUnknownEvent;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use stdClass;

class TypedCollectionTest extends TestCase
{
    public function test_that_invalid_item_throws_exception(): void
    {
        // given
        $collection = new RosterEventCollection();

        // then
        $this->expectException(InvalidCollectionItemException::class);

        // when
        $collection->push(new class {
        });
    }

    public function test_that_empty_collection_has_no_previous_item(): void
    {
        // given
        $collection = new RosterEventCollection();

        // when
        $previousItem = $collection->getNearestPreviousEventOfType(0, stdClass::class);

        // then
        $this->assertNull($previousItem);
    }

    public function test_that_empty_collection_has_no_next_item(): void
    {
        // given
        $collection = new RosterEventCollection();

        // when
        $nextItem = $collection->getNearestNextEventOfType(0, stdClass::class);

        // then
        $this->assertNull($nextItem);
    }

    public function test_that_single_item_collection_has_no_previous_item(): void
    {
        // given
        $collection = new RosterEventCollection([ new RosterCheckInEvent(Carbon::now()) ]);

        // when
        $previousItem = $collection->getNearestPreviousEventOfType(0, stdClass::class);

        // then
        $this->assertNull($previousItem);
    }

    public function test_that_single_item_collection_has_no_next_item(): void
    {
        // given
        $collection = new RosterEventCollection([ new RosterCheckInEvent(Carbon::now()) ]);

        // when
        $nextItem = $collection->getNearestNextEventOfType(0, stdClass::class);

        // then
        $this->assertNull($nextItem);
    }

    public function test_that_collection_is_missing_previous_item(): void
    {
        // given
        $collection = new RosterEventCollection([
            new RosterCheckInEvent(Carbon::now()),
            new RosterUnknownEvent(Carbon::now()),
        ]);

        $collection->push(new RosterCheckOutEvent(Carbon::now()->addHour()));

        // when
        $previousItem = $collection->getNearestPreviousEventOfType(2, RosterFlightEvent::class);

        // then
        $this->assertNull($previousItem);
    }

    public function test_that_collection_is_missing_next_item(): void
    {
        // given
        $collection = new RosterEventCollection([
            new RosterCheckInEvent(Carbon::now()),
            new RosterUnknownEvent(Carbon::now()),
        ]);

        $collection->push(new RosterCheckOutEvent(Carbon::now()->addHour()));

        // when
        $nextItem = $collection->getNearestNextEventOfType(0, RosterFlightEvent::class);

        // then
        $this->assertNull($nextItem);
    }
}
