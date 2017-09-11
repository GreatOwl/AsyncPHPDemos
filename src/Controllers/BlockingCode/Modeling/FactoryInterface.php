<?php


namespace Async\Demo\Controllers\BlockingCode\Modeling;


interface FactoryInterface
{
    public function createModel(AccessInterface $data);
}
