<?php


namespace Async\Demo\Queueing;


use Psr\SimpleCache\CacheInterface;

class Status
{
    private $cacheQueue;

    public function __construct(CacheInterface $cacheQueue)
    {
        $this->cacheQueue = $cacheQueue;
    }

    public function getMessages()
    {
        return $this->cacheQueue->get('queuingMessages', []);
    }

    public function getStatus()
    {
        return $this->cacheQueue->get('queuingStatus', ['started' => [], 'complete' => []]);
    }

    public function getExecutionTime()
    {
        $start = $this->cacheQueue->get('queueTimeStart', 0);
        $last = $this->cacheQueue->get('queueTimeLast', 0);
        $total = floor($last - $start);

        $minutes = floor($total / 60);
        $seconds = ($total / 60 - $minutes) * 60;

        return $this->format($minutes) . ' m, ' . $this->format($seconds) . ' s';
    }

    private function format($number)
    {
        return number_format($number, 0, '.', '');
    }
}
