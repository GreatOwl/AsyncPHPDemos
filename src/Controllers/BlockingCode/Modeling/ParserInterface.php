<?php


namespace Async\Demo\Controllers\BlockingCode\Modeling;


interface ParserInterface
{
    public function parse($rawData): array;
}
