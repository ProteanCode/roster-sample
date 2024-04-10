<?php

namespace Tests\Smoke\CCNX;

use App\Classes\Enums\ParserSource;
use App\Models\RosterEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\CCNXParserTest;

class ImportFileTest extends CCNXParserTest
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
    }

    public function test_that_import_works()
    {
        $request = $this->post('/api/events/', [
            'source_type' => ParserSource::CCNX->value,
            'source_data' => $this->validFile
        ]);

        $this->assertEquals(201, $request->getStatusCode());
        $this->assertGreaterThan(0, RosterEvent::query()->count());
    }
}
