<?php

namespace Tests;

use Illuminate\Http\UploadedFile;

class ParserTest extends TestCase
{
    const PARSERS_DIR_PATH = '/Files/Parsers';

    protected function makeParserFilePath(string $parser, string $filename): string
    {
        return base_path('/tests/' . self::PARSERS_DIR_PATH . '/' . $parser . '/' . $filename;
    }
}
