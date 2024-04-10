<?php

namespace App\Classes\Parsers\CCNX;

use App\Classes\Collections\RosterEventCollection;
use App\Classes\Dtos\RosterCheckInEvent;
use App\Classes\Dtos\RosterCheckOutEvent;
use App\Classes\Dtos\RosterEvent;
use App\Classes\Dtos\RosterFlightEvent;
use App\Classes\Dtos\RosterUnknownEvent;
use App\Classes\Dtos\TimeRange;
use App\Classes\Parsers\CCNX\Factories\RosterActivityStrategyFactory;
use App\Classes\Parsers\CCNX\Factories\RosterCheckInOutStrategyFactory;
use App\Classes\Parsers\CCNX\Strategies\CheckInRecordStrategy;
use App\Classes\Parsers\CCNX\Strategies\CheckOutRecordStrategy;
use App\Classes\Parsers\CCNX\Strategies\IRosterStrategy;
use App\Classes\Parsers\Exceptions\InvalidParserSourceContent;
use App\Classes\Parsers\IParser;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use IvoPetkov\HTML5DOMDocument;
use IvoPetkov\HTML5DOMElement;

class HtmlParser implements IParser
{
    private array|null $allRows = null;
    private array $headers = [];

    public function __construct(protected UploadedFile $uploadedFile)
    {
    }

    public function getName(): string
    {
        return 'CCNX';
    }

    public function parse(): RosterEventCollection
    {
        $rosters = new RosterEventCollection([]);
        $addRosterStrategy = fn(?IRosterStrategy $rosterStrategy) => $rosterStrategy ? $rosters->add($rosterStrategy->getEvent()) : null;

        $dom = new HTML5DOMDocument();
        $dom->loadHTML($this->getRawFileContent());

        $dataRows = $this->getDataRows($dom);
        $periodRange = $this->getPeriodRange($dom);

        $currentDate = $periodRange->from->clone()->subDay();

        $headers = $this->getHeaders($dom);

        foreach ($dataRows as $dataRow) {
            if ($this->isNewDayRow($dataRow)) {
                $currentDate = $currentDate->clone()->addDay();
            }

            $values = $this->getRowValues($dataRow);

            $checkInOutStrategy = (new RosterCheckInOutStrategyFactory($headers))
                ->resolve($currentDate, $values);

            $activityStrategy = (new RosterActivityStrategyFactory($headers))
                ->resolve($currentDate, $values);

            if ($checkInOutStrategy === null && $activityStrategy === null) {
                $rosters->add(new RosterUnknownEvent($currentDate)); // fallback event

                continue;
            }

            if ($checkInOutStrategy !== null) {
                $isInterconnectedStrategy = $checkInOutStrategy instanceof CheckInRecordStrategy || $checkInOutStrategy instanceof CheckOutRecordStrategy;

                if (!$isInterconnectedStrategy) {
                    $addRosterStrategy($checkInOutStrategy);

                    continue;
                }

                if ($checkInOutStrategy instanceof CheckInRecordStrategy) {
                    $addRosterStrategy($checkInOutStrategy);
                    $addRosterStrategy($activityStrategy);
                }

                if ($checkInOutStrategy instanceof CheckOutRecordStrategy) {
                    $addRosterStrategy($activityStrategy);
                    $addRosterStrategy($checkInOutStrategy);
                }
            } else {
                $addRosterStrategy($activityStrategy);
            }
        }

        return $rosters->map(function (RosterEvent $rosterEvent, int $index) use ($rosters) {
            if ($rosterEvent instanceof RosterCheckInEvent) {
                $nextFlight = $rosters->getNearestNextEventOfType($index, RosterFlightEvent::class);
                if ($nextFlight instanceof RosterFlightEvent) {
                    $rosterEvent->setLocation($nextFlight->from);
                }
            }

            if ($rosterEvent instanceof RosterCheckOutEvent) {
                $previousFlight = $rosters->getNearestPreviousEventOfType($index, RosterFlightEvent::class);
                if ($previousFlight instanceof RosterFlightEvent) {
                    $rosterEvent->setLocation($previousFlight->to);
                }
            }

            return $rosterEvent;
        });
    }

    protected function getCellValue(HTML5DOMElement $element): string|int|float
    {
        $content = $element->getTextContent();

        if (is_numeric($content)) {
            if ($content > PHP_INT_MAX) {
                return (float)$content;
            }

            return (int)$content;
        }

        $removablePhrases = [
            "\u{A0}"
        ];

        return str_replace($removablePhrases, '', $content);
    }

    private function getHeaders(HTML5DOMDocument $dom): array
    {
        if (!empty($this->headers)) {
            return $this->headers;
        }

        $headerRow = $this->getHeaderRow($dom);

        foreach ($this->getRowTds($headerRow) as $td) {
            $this->headers[] = $this->getCellValue($td);
        }

        return $this->headers;
    }

    private function getRowValues(HTML5DOMElement $rosterRow): array
    {
        return array_map(fn($item) => $this->getCellValue($item), $this->getRowTds($rosterRow));
    }

    /**
     * @param HTML5DOMDocument $dom
     * @return HTML5DOMElement
     */
    private function getHeaderRow(HTML5DOMDocument $dom): HTML5DOMElement
    {
        $rows = $this->getAllRows($dom);

        return $rows[0];
    }

    private function isNewDayRow(HTML5DOMElement $row): bool
    {
        if ($dateTd = $row->querySelector('td.activitytablerow-date')) {
            return !empty($dateTd->getTextContent());
        }

        return false;
    }

    private function getPeriodRange(HTML5DOMDocument $dom): TimeRange
    {
        // parsing library misses option:checked selector
        /** @var HTML5DOMElement $option */
        $option = collect($dom->querySelectorAll('select[name*="periodSelect"] option'))
            ->filter(fn(HTML5DOMElement $option) => $option->hasAttribute('selected'))
            ->firstOrFail();

        $periods = explode('|', $option->getAttribute('value'));

        return new TimeRange(
            Carbon::createFromFormat('Y-m-d', $periods[0])->setTimezone('UTC'),
            Carbon::createFromFormat('Y-m-d', $periods[1])->setTimezone('UTC'),
        );
    }

    /**
     * @param HTML5DOMDocument $dom
     * @return HTML5DOMElement[]
     */
    private function getDataRows(HTML5DOMDocument $dom): array
    {
        $rows = $this->getAllRows($dom);

        return array_values(array_slice($rows, 1));
    }

    /**
     * @param HTML5DOMElement $element
     * @return HTML5DOMElement[]
     */
    private function getRowTds(HTML5DOMElement $element): array
    {
        return $element->querySelectorAll('td')->getArrayCopy();
    }

    /**
     * @param HTML5DOMDocument $dom
     * @return HTML5DOMElement[]
     */
    private function getAllRows(HTML5DOMDocument $dom): array
    {
        if ($this->allRows === null) {
            $this->allRows = $dom->querySelectorAll('[id*="Main_activity_table"] tbody tr')->getArrayCopy();
        }

        return $this->allRows;
    }

    private function getRawFileContent(): string
    {
        $content = $this->uploadedFile->getContent();
        if (!$content) {
            throw new InvalidParserSourceContent($this->getName());
        }

        return $content;
    }
}
