<?php

namespace Tests;

class ParserTest extends TestCase
{
    const PARSERS_DIR_PATH = '/Files/Parsers';

    protected function makeParserFilePath(string $filename, string $parser): string
    {
        return base_path('/tests/' . self::PARSERS_DIR_PATH . '/' . $parser . '/' . $filename);
    }
}
