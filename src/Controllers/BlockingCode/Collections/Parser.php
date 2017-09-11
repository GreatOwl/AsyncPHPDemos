<?php


namespace Async\Demo\Controllers\BlockingCode\Collections;


use Async\Demo\Controllers\BlockingCode\Modeling\ParserInterface;

class Parser implements ParserInterface
{

    public function parse($rawData): array
    {
        return $rawData;
    }
}
