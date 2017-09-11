<?php


namespace Async\Demo\Controllers\BlockingCode\Collections;


use Async\Demo\Controllers\BlockingCode\Modeling\AccessInterface;
use Async\Demo\Controllers\BlockingCode\Modeling\Data;

class Collection implements \IteratorAggregate, \Countable
{
    private $rawData;
    private $data;

    private $resolved = false;
    private $synchronous;

    private $operations = [];

    public function __construct(AccessInterface $rawData, $synchronous = true)
    {
        $this->rawData = $rawData;
        $this->synchronous = $synchronous;
    }

    public function makeAsync()
    {
        $this->synchronous = false;

        return $this;
    }

    public function filter(callable $filter): Collection
    {
        return $this->addOperation(
            function ($data) use ($filter) {
                return array_filter($data, $filter);
            }
        );
    }

    public function sort(callable $sort): Collection
    {
        return $this->addOperation(
            function ($data) use ($sort) {
                usort($data, $sort);

                return $data;
            }
        );
    }

    public function map(callable $transform): Collection
    {
        return $this->addOperation(
            function ($data) use ($transform) {
                return array_map($transform, $data);
            }
        );
    }

    private function addOperation(callable $operation): Collection
    {
        $operations = $this->operations;
        $operations[] = $operation;
        $col = new static($this->rawData);
        $col->operations = $operations;
        $col->synchronous = $this->synchronous;
        if ($this->synchronous) {
            $col->resolve();
        }

        return $col;
    }

    private function loadData(): array
    {
        if (is_null($this->data)) {
            $this->data = $this->rawData->toArray();
        }

        return $this->data;
    }

    private function resolve()
    {
        if (!$this->resolved) {
            $data = $this->loadData();
            /** @var callable $operation */
            foreach ($this->operations as $operation) {
                $data = $operation($data);
            }
            $this->resolved = true;
            $this->rawData = new Data($data);
            $this->data = null;
            $this->operations = [];
        }
    }

    public function withElement($element): Collection
    {
        return $this->addOperation(
            function ($data) use ($element) {
                $data[] = $element;

                return $data;
            }
        );
    }

    public function merge(Collection $collection): Collection
    {
        return $this->addOperation(
            function ($data) use ($collection) {
                return array_merge($data, $collection->toArray());
            }
        );
    }

    public function toArray(): array
    {
        $this->resolve();

        return $this->loadData();
    }

    public function count(): int
    {
        return count($this->toArray());
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->toArray());
    }
}
