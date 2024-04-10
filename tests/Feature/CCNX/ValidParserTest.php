<?php

namespace Tests\Feature\CCNX;

use App\Classes\Parsers\CCNX;
use Illuminate\Http\UploadedFile;
use Tests\ParserTest;

class ParserTest extends ParserTest
{
    public function __construct(string $name)
    {
        parent::__construct($name);

        $file = UploadedFile::fake()->createWithContent(
            basename(),
            file_get_contents($this->makeParserFilePath())
        );

        $this->parser = (new CCNX());
    }

    /**
     * A basic test example.
     */
    public function test_sth(): void
    {

    }
}
