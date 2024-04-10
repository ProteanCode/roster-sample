<?php

namespace Tests;

class CCNXParserTest extends ParserTest
{
    protected function getValidFilePath(): string
    {
        return $this->makeParserFilePath('valid.html');
    }

    protected function makeParserFilePath(string $filename, string $parser = 'CCNX'): string
    {
        return parent::makeParserFilePath($filename, $parser);
    }
}
