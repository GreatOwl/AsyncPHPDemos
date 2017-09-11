<?php


namespace Async\Demo\Controllers\BlockingCode\Modeling;


interface AccessInterface extends \ArrayAccess, \IteratorAggregate, \Serializable, \Countable
{
    public function withLoader(callable $loader);

    public function toArray();
}
