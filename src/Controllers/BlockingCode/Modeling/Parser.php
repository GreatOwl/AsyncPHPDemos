<?php


namespace Async\Demo\Controllers\BlockingCode\Modeling;


class Parser implements ParserInterface
{

    public function parse($rawData): array
    {
        return json_decode($rawData, true);
    }
}
