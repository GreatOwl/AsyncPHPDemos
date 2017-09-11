<?php


namespace Async\Demo\Controllers\BlockingCode\Modeling;

use Http\Promise\Promise as PromiseInterface;
use Psr\Http\Message\ResponseInterface;

class Data implements AccessInterface
{

    /** @var ExtractionLoader $extractionLoader */
    private $extractionLoader;
    private $data;

    private $rawData;

    protected $loader;

    protected $handlers;

    private $loaded = false;

    public function __construct($data = [], ExtractionLoader $extractionLoader = null, $handlers = [])
    {
        $this->rawData = $data;
        $this->extractionLoader = $extractionLoader;
        $this->handlers = $handlers;
    }

    public function serialize()
    {
        return serialize($this->load());
    }

    public function unserialize($serialized)
    {
        $this->data = unserialize($serialized);
    }

    /**
     * @param callable $loader
     *
     * @return AccessInterface
     */
    public function withLoader(callable $loader): AccessInterface
    {
        /** @var AccessInterface $data */
        $this->loader = $loader;

        return $this;
    }

    private function load()
    {
        if (is_null($this->data)) {
            $data = $this->rawData;
            if ($data instanceof PromiseInterface) {
                /** @var ResponseInterface $response */
                $data = $data->wait();
            }

            $data = $this->runResponseHandlers($data);
            if ($this->extractionLoader instanceof ExtractionLoader) {
                $data = $this->extractionLoader->extract($data);
            }
            $this->data = $data;
        }
        if (!$this->loaded && is_callable($this->loader)) {
            $this->data = call_user_func($this->loader, $this->data);
            $this->loaded = true;
        }

        return $this->data;
    }

    protected function runResponseHandlers($result)
    {
        /** @var callable $handler */
        foreach ($this->handlers as $handler) {
            $result = $handler($result);
        }

        return $result;
    }

    public function count()
    {
        return count($this->load());
    }

    public function offsetGet($offset)
    {
        return $this->load()[$offset];
    }

    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->load());
    }

    public function offsetSet($offset, $value)
    {
        $this->data = ($this->load()[$offset] = $value);
    }

    public function offsetUnset($offset)
    {
        $data = $this->load();
        unset($data[$offset]);
        $this->data = $data;
    }

    public function toArray()
    {
        return iterator_to_array($this->getIterator());
    }

    public function getIterator()
    {
        if ($this->extractionLoader instanceof ExtractionLoader) {
            return $this->getGenerator();
        };

        return new \ArrayIterator($this->load());
    }

    private function getGenerator()
    {
        foreach ($this->load() as $data) {
            $model = $this->extractionLoader->loadModel($data);
            yield $model;
        }
    }
}
