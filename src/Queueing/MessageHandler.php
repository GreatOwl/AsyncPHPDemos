<?php


namespace Async\Demo\Queueing;

use Psr\SimpleCache\CacheInterface;

class MessageHandler
{

    private $cacheQueue;

    public function __construct(CacheInterface $cache)
    {
        $this->cacheQueue = $cache;
    }

    public function handleMessage($message)
    {
        if (!is_null($message)) {
            $this->updateStatus($message);
            sleep($message['delay']);
            $this->updateComplete($message);
            $this->updateTime('queueTimeLast');

            return true;
        }

        return false;
    }

    private function updateStatus($message)
    {
        $progress = 'started';
        $status = $this->cacheQueue->get('queuingStatus', ['started' => [], 'complete' => []]);
        $status[$progress][$message['id']] = $progress . " ~ " . $message['name'];
        $this->cacheQueue->set('queuingStatus', $status, 60);
    }

    private function updateComplete($message)
    {
        $progress = 'complete';
        $status = $this->cacheQueue->get('queuingStatus', ['started' => [], 'complete' => []]);
        $status[$progress][] = $progress . " ~ " . $message['name'];
        unset($status['started'][$message['id']]);
        $this->cacheQueue->set('queuingStatus', $status, 60);
    }

    private function updateTime($timeKey)
    {
        $this->cacheQueue->set($timeKey, microtime(true), 180);
    }

}
