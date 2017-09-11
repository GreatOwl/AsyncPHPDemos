<?php


namespace Async\Demo\Queueing;


use Psr\SimpleCache\CacheInterface;

class QueueHandler
{
    private $cacheQueue;

    public function __construct(CacheInterface $cache)
    {
        $this->cacheQueue = $cache;
    }

    public function isQueueActive()
    {
        return $this->cacheQueue->get('queuingActive', false) === true;
    }

    public function activateQueue()
    {
        if (!$this->isQueueActive()) {
            $this->cacheQueue->set('queuingActive', true, 600);
            $this->updateTime('queueTimeStart');
            $this->updateTime('queueTimeLast');
        }
    }

    public function deactivateQueue()
    {
        if ($this->isQueueActive()) {
            $this->cacheQueue->set('queuingActive', false);
            usleep(100000);
            $this->deactivateQueue();
        }
    }

    private function updateTime($timeKey)
    {
        $this->cacheQueue->set($timeKey, microtime(true), 180);
    }
}
