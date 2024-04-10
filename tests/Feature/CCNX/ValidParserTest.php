<?php

namespace Tests\Feature\CCNX;

use App\Classes\Collections\RosterEventCollection;
use App\Classes\Dtos\RosterCheckInEvent;
use App\Classes\Dtos\RosterCheckOutEvent;
use App\Classes\Dtos\RosterDayOffEvent;
use App\Classes\Dtos\RosterFlightEvent;
use App\Classes\Dtos\RosterStandbyEvent;
use App\Classes\Dtos\RosterUnknownEvent;
use App\Classes\Parsers\CCNX\HtmlParser;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;

class ValidParserTest extends CCNXParserTest
{
    const DATE_FORMAT = 'Y-m-d';
    const DATETIME_FORMAT = 'Y-m-d H:i';

    protected static ?RosterEventCollection $parsedRows = null;

    protected function setUp(): void
    {
        parent::setUp();

        $validFilePath = $this->getValidFilePath();

        $validFile = UploadedFile::fake()->createWithContent(
            basename($validFilePath),
            file_get_contents($validFilePath)
        );

        $this->parser = new HtmlParser($validFile);

        if (self::$parsedRows === null) {
            self::$parsedRows = $this->parser->parse();
        }
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        self::$parsedRows = null;
    }

    /**
     * @dataProvider provideValidParserData
     */
    public function test_that_parsed_file_have_events_with_valid_data(
        int     $index,
        string  $expectedEventClass,
        string  $expectedEventDate,
        ?string $expectedCheckInDateTime,
        ?string $expectedCheckOutDateTime,
        ?string $from,
        ?string $to,
        ?string $expectedFlightNumber,
        ?string $expectedDepartureTime,
        ?string $expectedArrivalTime,
        ?string $checkInOutLocation,
    ): void
    {
        // Given
        $item = self::$parsedRows->get($index);

        // When + Then
        $this->assertEquals($expectedEventClass, get_class($item));

        if ($item instanceof RosterFlightEvent) {
            $this->assertFlightEvent($index, $item, $expectedEventDate, $from, $to, $expectedFlightNumber, $expectedDepartureTime, $expectedArrivalTime);
        }

        if ($item instanceof RosterCheckInEvent) {
            $this->assertCheckInEvent($index, $item, $expectedCheckInDateTime, $checkInOutLocation);
        }

        if ($item instanceof RosterCheckOutEvent) {
            $this->assertCheckOutEvent($index, $item, $expectedCheckOutDateTime, $checkInOutLocation);
        }

        if ($item instanceof RosterUnknownEvent) {
            $this->assertUnknownEvent($index, $item, $expectedEventDate);
        }
    }

    static public function provideValidParserData(): array
    {
        $start = Carbon::create(2022, 1, 10)
            ->setTime(0, 0, 0, 0)
            ->setTimezone('UTC');

        $nextDay = fn(Carbon $carbon) => $carbon->addDay();
        $format = fn(Carbon $carbon) => $carbon->format(self::DATE_FORMAT);

        $formatWithTime = function (Carbon $carbon, int $time) {
            $as4 = str_pad($time, 4, '0', STR_PAD_LEFT);
            $hour = (int)substr($as4, 0, 2);
            $minute = (int)substr($as4, 2, 4);

            return $carbon->clone()
                ->setTimezone('UTC')
                ->setTime($hour, $minute, 0, 0)
                ->format(self::DATETIME_FORMAT);
        };

        return [
            [
                0, // index
                RosterCheckInEvent::class, // expected event class
                $format($start), // date of the event
                $formatWithTime($start, 745), // check in date
                null, // check out date
                null, // from
                null, // to,
                null, // flight-number
                null, // stdz
                null, // staz
                'KRP', // check in/out location
            ],
            [
                1,
                RosterFlightEvent::class,
                $format($start),
                null,
                null,
                'KRP',
                'CPH',
                'DX77',
                $formatWithTime($start, 845),
                $formatWithTime($start, 935),
                null, // check in/out location
            ],
            [
                2,
                RosterFlightEvent::class,
                $format($start),
                null,
                null,
                'CPH',
                'KRP',
                'DX80',
                $formatWithTime($start, 1345),
                $formatWithTime($start, 1435),
                null, // check in/out location
            ],
            [
                3,
                RosterFlightEvent::class,
                $format($start),
                null,
                null,
                'KRP',
                'CPH',
                'DX83',
                $formatWithTime($start, 1520),
                $formatWithTime($start, 1610),
                null, // check in/out location
            ],
            [
                4,
                RosterFlightEvent::class,
                $format($start),
                null,
                null,
                'CPH',
                'KRP',
                'DX82',
                $formatWithTime($start, 1645),
                $formatWithTime($start, 1735),
                null, // check in/out location
            ],
            [
                5,  // index
                RosterCheckOutEvent::class, // expected event class
                $format($start), // date of the event
                null, // check in date
                $formatWithTime($start, 1755), // check out date
                null, // from
                null, // to,
                null, // flight-number
                null, // stdz
                null, // staz
                'KRP', // check in/out location
            ],
            [
                6,  // index
                RosterCheckInEvent::class, // expected event class
                $format($nextDay($start)), // date of the event
                $formatWithTime($start, 745), // check in date
                null, // check out date
                null, // from
                null, // to,
                null, // flight-number
                null, // stdz
                null, // staz
                'KRP', // check in/out location
            ],
            [
                7,  // index
                RosterFlightEvent::class, // expected event class
                $format($start), // date of the event
                null, // check in date
                null, // check out date
                'KRP', // from
                'CPH', // to
                'DX77', // flight-number
                $formatWithTime($start, 845), // stdz
                $formatWithTime($start, 935), // staz
                null, // check in/out location
            ],
            [
                8,  // index
                RosterFlightEvent::class, // expected event class
                $format($start), // date of the event
                null, // check in date
                null, // check out date
                'CPH', // from
                'KRP', // to
                'DX80', // flight-number
                $formatWithTime($start, 1345), // stdz
                $formatWithTime($start, 1435), // staz
                null, // check in/out location
            ],
            [
                9,  // index
                RosterFlightEvent::class, // expected event class
                $format($start), // date of the event
                null, // check in date
                null, // check out date
                'KRP', // from
                'CPH', // to
                'DX83', // flight-number
                $formatWithTime($start, 1520), // stdz
                $formatWithTime($start, 1610), // staz
                null, // check in/out location
            ],
            [
                10,  // index
                RosterFlightEvent::class, // expected event class
                $format($start), // date of the event
                null, // check in date
                null, // check out date
                'CPH', // from
                'KRP', // to
                'DX82', // flight-number
                $formatWithTime($start, 1645), // stdz
                $formatWithTime($start, 1735), // staz
                null, // check in/out location
            ],
            [
                11,  // index
                RosterCheckOutEvent::class, // expected event class
                $format($start), // date of the event
                null, // check in date
                $formatWithTime($start, 1755), // check out date
                null, // from
                null, // to,
                null, // flight-number
                null, // stdz
                null, // staz
                'KRP', // check in/out location
            ],
            [
                12,  // index
                RosterDayOffEvent::class, // expected event class
                $format($nextDay($start)), // date of the event
                null, // check in date
                null, // check out date
                null, // from
                null, // to,
                null, // flight-number
                null, // stdz
                null, // staz
                null, // check in/out location
            ],
            [
                13,  // index
                RosterDayOffEvent::class, // expected event class
                $format($nextDay($start)), // date of the event
                null, // check in date
                null, // check out date
                null, // from
                null, // to,
                null, // flight-number
                null, // stdz
                null, // staz
                null, // check in/out location
            ],
            [
                14,  // index
                RosterDayOffEvent::class, // expected event class
                $format($nextDay($start)), // date of the event
                null, // check in date
                null, // check out date
                null, // from
                null, // to,
                null, // flight-number
                null, // stdz
                null, // staz
                null, // check in/out location
            ],
            [
                15,  // index
                RosterStandbyEvent::class, // expected event class
                $format($nextDay($start)), // date of the event
                $formatWithTime($start, 500), // check in date
                $formatWithTime($start, 1700), // check out date
                'KRP', // from
                'KRP', // to,
                null, // flight-number
                null, // stdz
                null, // staz
                null, // check in/out location
            ],
            [
                16,  // index
                RosterStandbyEvent::class, // expected event class
                $format($nextDay($start)), // date of the event
                $formatWithTime($start, 500), // check in date
                $formatWithTime($start, 1700), // check out date
                'KRP', // from
                'KRP', // to,
                null, // flight-number
                null, // stdz
                null, // staz
                null, // check in/out location
            ],
            [
                17,  // index
                RosterCheckInEvent::class, // expected event class
                $format($nextDay($start)), // date of the event
                $formatWithTime($start, 745), // check in date
                null, // check out date
                null, // from
                null, // to,
                null, // flight-number
                null, // stdz
                null, // staz
                'KRP', // check in/out location
            ],
            [
                18,  // index
                RosterFlightEvent::class, // expected event class
                $format($start), // date of the event
                null, // check in date
                null, // check out date
                'KRP', // from
                'CPH', // to,
                'DX77', // flight-number
                $formatWithTime($start, 845), // stdz
                $formatWithTime($start, 935), // staz
                null, // check in/out location
            ],
            [
                19,  // index
                RosterFlightEvent::class, // expected event class
                $format($start), // date of the event
                null, // check in date
                null, // check out date
                'CPH', // from
                'KRP', // to
                'DX80', // flight-number
                $formatWithTime($start, 1345), // stdz
                $formatWithTime($start, 1435), // staz
                null, // check in/out location
            ],
            [
                20,  // index
                RosterFlightEvent::class, // expected event class
                $format($start), // date of the event
                null, // check in date
                null, // check out date
                'KRP', // from
                'CPH', // to
                'DX83', // flight-number
                $formatWithTime($start, 1520), // stdz
                $formatWithTime($start, 1610), // staz
                null, // check in/out location
            ],
            [
                21,  // index
                RosterFlightEvent::class, // expected event class
                $format($start), // date of the event
                null, // check in date
                null, // check out date
                'CPH', // from
                'KRP', // to
                'DX82', // flight-number
                $formatWithTime($start, 1645), // stdz
                $formatWithTime($start, 1735), // staz
                null, // check in/out location
            ],
            [
                22,  // index
                RosterCheckOutEvent::class, // expected event class
                $format($start), // date of the event
                null, // check in date
                $formatWithTime($start, 1755), // check out date
                null, // from
                null, // to,
                null, // flight-number
                null, // stdz
                null, // staz
                'KRP', // check in/out location
            ],
            [
                23,  // index
                RosterDayOffEvent::class, // expected event class
                $format($nextDay($start)), // date of the event
                null, // check in date
                null, // check out date
                null, // from
                null, // to,
                null, // flight-number
                null, // stdz
                null, // staz
                null, // check in/out location
            ],
            [
                24,  // index
                RosterDayOffEvent::class, // expected event class
                $format($nextDay($start)), // date of the event
                null, // check in date
                null, // check out date
                null, // from
                null, // to,
                null, // flight-number
                null, // stdz
                null, // staz
                null, // check in/out location
            ],
            [
                25,  // index
                RosterCheckInEvent::class, // expected event class
                $format($nextDay($start)), // date of the event
                $formatWithTime($start, 500), // check in date
                null, // check out date
                null, // from
                null, // to,
                null, // flight-number
                null, // stdz
                null, // staz
                'KRP', // check in/out location
            ],
            [
                26,  // index
                RosterFlightEvent::class, // expected event class
                $format($start), // date of the event
                null, // check in date
                null, // check out date
                'KRP', // from
                'CPH', // to
                'DX73', // flight-number
                $formatWithTime($start, 600), // stdz
                $formatWithTime($start, 650), // staz
                null, // check in/out location
            ],
            [
                27,  // index
                RosterFlightEvent::class, // expected event class
                $format($start), // date of the event
                null, // check in date
                null, // check out date
                'CPH', // from
                'KRP', // to
                'DX74', // flight-number
                $formatWithTime($start, 720), // stdz
                $formatWithTime($start, 810), // staz
                null, // check in/out location
            ],
            [
                28,  // index
                RosterFlightEvent::class, // expected event class
                $format($start), // date of the event
                null, // check in date
                null, // check out date
                'KRP', // from
                'CPH', // to
                'DX77', // flight-number
                $formatWithTime($start, 845), // stdz
                $formatWithTime($start, 935), // staz
                null, // check in/out location
            ],
            [
                29,  // index
                RosterFlightEvent::class, // expected event class
                $format($start), // date of the event
                null, // check in date
                null, // check out date
                'CPH', // from
                'KRP', // to
                'DX80', // flight-number
                $formatWithTime($start, 1315), // stdz
                $formatWithTime($start, 1410), // staz
                null, // check in/out location
            ],
            [
                30,  // index
                RosterCheckOutEvent::class, // expected event class
                $format($start), // date of the event
                null, // check in date
                $formatWithTime($start, 1430), // check out date
                null, // from
                null, // to,
                null, // flight-number
                null, // stdz
                null, // staz
                'KRP', // check in/out location
            ],
            [
                31,  // index
                RosterCheckInEvent::class, // expected event class
                $format($nextDay($start)), // date of the event
                $formatWithTime($start, 500), // check in date
                null, // check out date
                null, // from
                null, // to,
                null, // flight-number
                null, // stdz
                null, // staz
                'KRP', // check in/out location
            ],
            [
                32,  // index
                RosterFlightEvent::class, // expected event class
                $format($start), // date of the event
                null, // check in date
                null, // check out date
                'KRP', // from
                'CPH', // to
                'DX73', // flight-number
                $formatWithTime($start, 600), // stdz
                $formatWithTime($start, 650), // staz
                null, // check in/out location
            ],
            [
                33,  // index
                RosterFlightEvent::class, // expected event class
                $format($start), // date of the event
                null, // check in date
                null, // check out date
                'CPH', // from
                'KRP', // to
                'DX74', // flight-number
                $formatWithTime($start, 720), // stdz
                $formatWithTime($start, 810), // staz
                null, // check in/out location
            ],
            [
                34,  // index
                RosterFlightEvent::class, // expected event class
                $format($start), // date of the event
                null, // check in date
                null, // check out date
                'KRP', // from
                'CPH', // to
                'DX77', // flight-number
                $formatWithTime($start, 845), // stdz
                $formatWithTime($start, 935), // staz
                null, // check in/out location
            ],
            [
                35,  // index
                RosterFlightEvent::class, // expected event class
                $format($start), // date of the event
                null, // check in date
                null, // check out date
                'CPH', // from
                'KRP', // to
                'DX80', // flight-number
                $formatWithTime($start, 1345), // stdz
                $formatWithTime($start, 1440), // staz
                null, // check in/out location
            ],
            [
                36,  // index
                RosterCheckOutEvent::class, // expected event class
                $format($start), // date of the event
                null, // check in date
                $formatWithTime($start, 1500), // check out date
                null, // from
                null, // to,
                null, // flight-number
                null, // stdz
                null, // staz
                'KRP', // check in/out location
            ],
            [
                37,  // index
                RosterUnknownEvent::class, // expected event class
                $format($nextDay($start)), // date of the event
                null, // check in date
                null, // check out date
                null, // from
                null, // to,
                null, // flight-number
                null, // stdz
                null, // staz
                null, // check in/out location
            ],
            [
                38,  // index
                RosterCheckInEvent::class, // expected event class
                $format($start), // date of the event
                $formatWithTime($start, 720), // check in date
                null, // check out date
                null, // from
                null, // to,
                null, // flight-number
                null, // stdz
                null, // staz
                'EBJ', // check in/out location
            ],
            [
                39,  // index
                RosterFlightEvent::class, // expected event class
                $format($start), // date of the event
                null, // check in date
                null, // check out date
                'EBJ', // from
                'SVG', // to
                'DX21', // flight-number
                $formatWithTime($start, 820), // stdz
                $formatWithTime($start, 930), // staz
                null, // check in/out location
            ],
            [
                40,  // index
                RosterFlightEvent::class, // expected event class
                $format($start), // date of the event
                null, // check in date
                null, // check out date
                'SVG', // from
                'EBJ', // to
                'DX22', // flight-number
                $formatWithTime($start, 1000), // stdz
                $formatWithTime($start, 1110), // staz
                null, // check in/out location
            ],
            [
                41,  // index
                RosterUnknownEvent::class, // expected event class
                $format($start), // date of the event
                null, // check in date
                null, // check out date
                null, // from
                null, // to,
                null, // flight-number
                null, // stdz
                null, // staz
                null, // check in/out location
            ],
            [
                42,  // index
                RosterCheckInEvent::class, // expected event class
                $format($nextDay($start)), // date of the event
                $formatWithTime($start, 945), // check in date
                null, // check out date
                null, // from
                null, // to,
                null, // flight-number
                null, // stdz
                null, // staz
                'KRP', // check in/out location
            ],
            [
                43,  // index
                RosterFlightEvent::class, // expected event class
                $format($start), // date of the event
                null, // check in date
                null, // check out date
                'KRP', // from
                'CPH', // to
                'DX79', // flight-number
                $formatWithTime($start, 1045), // stdz
                $formatWithTime($start, 1135), // staz
                null, // check in/out location
            ],
            [
                44,  // index
                RosterFlightEvent::class, // expected event class
                $format($start), // date of the event
                null, // check in date
                null, // check out date
                'CPH', // from
                'KRP', // to
                'DX82', // flight-number
                $formatWithTime($start, 1600), // stdz
                $formatWithTime($start, 1650), // staz
                null, // check in/out location
            ],
            [
                45,  // index
                RosterFlightEvent::class, // expected event class
                $format($start), // date of the event
                null, // check in date
                null, // check out date
                'KRP', // from
                'CPH', // to
                'DX85', // flight-number
                $formatWithTime($start, 1720), // stdz
                $formatWithTime($start, 1810), // staz
                null, // check in/out location
            ],
            [
                46,  // index
                RosterFlightEvent::class, // expected event class
                $format($start), // date of the event
                null, // check in date
                null, // check out date
                'CPH', // from
                'KRP', // to
                'DX86', // flight-number
                $formatWithTime($start, 1840), // stdz
                $formatWithTime($start, 1930), // staz
                null, // check in/out location
            ],
            [
                47,  // index
                RosterCheckOutEvent::class, // expected event class
                $format($start), // date of the event
                null, // check in date
                $formatWithTime($start, 1950), // check out date
                null, // from
                null, // to,
                null, // flight-number
                null, // stdz
                null, // staz
                'KRP', // check in/out location
            ],
        ];
    }

    private function assertFlightEvent(int     $index, RosterFlightEvent $event,
                                       string  $expectedEventDate, string $from, string $to,
                                       string  $expectedFlightNumber,
                                       ?string $expectedDepartureDateTime,
                                       ?string $expectedArrivalDateTime,
    ): void
    {
        $this->assertEquals(
            $expectedEventDate,
            $event->getDate()->format(self::DATE_FORMAT),
            'Failed when checking ' . $index . '-th ' . get_class($event) . ' event'
        );

        $this->assertEquals($event->from, $from, 'Failed to check source location of ' . $index . '-th flight event');
        $this->assertEquals($event->to, $to, 'Failed to check target location of ' . $index . '-th flight event');
        $this->assertEquals($event->number, $expectedFlightNumber, 'Failed to check target flight number of ' . $index . '-th flight event');

        $this->assertEquals(
            $expectedDepartureDateTime,
            $event->stdz->format(self::DATETIME_FORMAT),
            'Failed when checking ' . $index . '-th ' . get_class($event) . ' departure datetime event'
        );

        $this->assertEquals(
            $expectedArrivalDateTime,
            $event->staz->format(self::DATETIME_FORMAT),
            'Failed when checking ' . $index . '-th ' . get_class($event) . ' arrival datetime event'
        );
    }

    private function assertCheckInEvent(int $index, RosterCheckInEvent $event, string $expectedCheckInDateTime, string $expectedCheckInLocation): void
    {
        $this->assertEquals(
            $expectedCheckInDateTime,
            $event->getDate()->format(self::DATETIME_FORMAT),
            'Failed when checking ' . $index . '-th ' . get_class($event) . ' event'
        );

        $this->assertEquals(
            $expectedCheckInLocation,
            $event->location,
            'Failed when checking ' . $index . '-th ' . get_class($event) . ' event for the right location'
        );
    }

    private function assertCheckOutEvent(int $index, RosterCheckOutEvent $event, string $expectedCheckOutDateTime, string $expectedCheckOutLocation): void
    {
        $this->assertEquals(
            $expectedCheckOutDateTime,
            $event->getDate()->format(self::DATETIME_FORMAT),
            'Failed when checking ' . $index . '-th ' . get_class($event) . ' event'
        );

        $this->assertEquals(
            $expectedCheckOutLocation,
            $event->location,
            'Failed when checking ' . $index . '-th ' . get_class($event) . ' event for the right location'
        );
    }

    private function assertUnknownEvent(int $index, RosterUnknownEvent $event, string $expectedEventDate): void
    {
        $this->assertEquals(
            $expectedEventDate,
            $event->getDate()->format(self::DATE_FORMAT),
            'Failed when checking ' . $index . '-th ' . get_class($event) . ' event'
        );
    }
}
