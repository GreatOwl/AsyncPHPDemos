<?php


namespace Async\Demo\Controllers\BlockingCode\Collections;


use Async\Demo\Controllers\BlockingCode\Modeling\AccessInterface;
use Async\Demo\Controllers\BlockingCode\Modeling\FactoryInterface;

class AnimalsFactory implements FactoryInterface
{

    public function createModel(AccessInterface $data)
    {
        return new Collection($data);
    }
}
