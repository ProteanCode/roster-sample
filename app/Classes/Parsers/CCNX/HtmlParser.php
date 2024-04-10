<?php

namespace App\Classes\Parsers\CCNX;

use Illuminate\Http\UploadedFile;
use IvoPetkov\HTML5DOMDocument;

class Parser
{
    public function __construct(protected UploadedFile $uploadedFile)
    {

    }

    public function parse() {
        $dom = new HTML5DOMDocument();

        $content = $this->uploadedFile->getContent();

        if(!$content) {
            throw new \RuntimeException("");
        }

        if(!$this->uploadedFile->getContent()) {

        }

        $dom->loadHTML($this->uploadedFile->get

        echo $dom->querySelector('h1')->innerHTML;

        dd($this->getRawContent());
    }
}
