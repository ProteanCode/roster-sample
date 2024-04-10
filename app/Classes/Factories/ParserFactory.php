<?php

namespace App\Classes\Factories;

use App\Classes\Enums\ParserSource;
use App\Classes\Parsers\CCNX\HtmlParser as CCNXHtmlParser;
use App\Classes\Parsers\IParser;
use Illuminate\Http\UploadedFile;
use InvalidArgumentException;

class ParserFactory
{
    public function __construct(private ParserSource $parserSource)
    {
    }

    public function make($sourceData): ?IParser
    {
        if ($this->parserSource === ParserSource::CCNX) {
            if (!$sourceData instanceof UploadedFile) {
                throw new InvalidArgumentException("Invalid source data for CCNX parser, expected a file");
            }

            return (new CCNXHtmlParser($sourceData));
        }

        return null;
    }
}
