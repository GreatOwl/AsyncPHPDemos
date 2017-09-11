<?php


namespace Async\Demo\Controllers\BlockingCode\Modeling;


class AnimalFactory implements FactoryInterface
{

    public function createModel(AccessInterface $data)
    {
        return new Animal($data);
    }
}
