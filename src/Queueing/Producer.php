<?php


namespace Async\Demo\Queueing;


use Psr\SimpleCache\CacheInterface;

class Producer
{
    private $cacheQueue;

    public function __construct(CacheInterface $cacheQueue)
    {
        $this->cacheQueue = $cacheQueue;
    }


    public function addMessages($count)
    {
        $messages = [];
        $delayTotal = 0;
        for ($i = 0; $i < $count; $i++) {
            $delay = mt_rand(1, 5);
            $messages[] = [
                'name' => $i,
                'delay' => $delay,
            ];
            $delayTotal = $delayTotal + $delay;
        }
        $queued = $this->cacheQueue->get('queuingMessages', []);
        $this->cacheQueue->set('queuingMessages', array_merge($messages, $queued), 120);
        $this->cacheQueue->set('queuingOriginal', $count, 300);

        $minutes = floor($delayTotal / 60);
        $seconds = ($delayTotal / 60 - $minutes) * 60;
        $delayString = $this->format($minutes) . ' m, ' . $this->format($seconds) . ' s';

        return [count($messages), $delayString];
    }

    private function format($number)
    {
        return number_format($number, 0, '.', '');
    }
}
