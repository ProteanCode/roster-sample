<?php

namespace Tests\Smoke\Events;

use App\Classes\Enums\ParserSource;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\CCNXParserTest;

class IndexEventsTest extends CCNXParserTest
{
    use RefreshDatabase;

    protected UploadedFile $validFile;

    protected function setUp(): void
    {
        parent::setUp();

        $validFilePath = $this->getValidFilePath();

        $this->validFile = UploadedFile::fake()->createWithContent(
            basename($validFilePath),
            file_get_contents($validFilePath)
        );

        $this->post('/api/events/', [
            'source_type' => ParserSource::CCNX->value,
            'source_data' => $this->validFile
        ]);
    }

    public function test_that_basic_querying_works()
    {
        $request = $this->get('/api/events');

        $this->assertEquals(200, $request->getStatusCode());
        $this->assertNotEmpty($request->getContent());
    }

    public function test_that_flights_querying_works()
    {
        $request = $this->get('/api/events?type=flight');

        $this->assertEquals(200, $request->getStatusCode());
        $this->assertNotEmpty($request->getContent());
    }

    public function test_that_date_range_querying_works()
    {
        $now = Carbon::now()->timestamp;
        $nextWeek = Carbon::now()->addWeek()->timestamp;

        $request = $this->get('/api/events?from=' . $now . '&to=' . $nextWeek);

        $this->assertEquals(200, $request->getStatusCode());
        $this->assertNotEmpty($request->getContent());
    }
}
