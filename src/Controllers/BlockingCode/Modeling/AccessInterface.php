<?php


namespace Async\Demo\Controllers\BlockingCode\Modeling;


interface AccessInterface extends \ArrayAccess, \IteratorAggregate, \Serializable, \Countable
{
    public function withLoader(callable $loader): AccessInterface;

    public function withoutLoader(): AccessInterface;

    public function toArray();
}
