<?php


namespace Async\Demo\Queueing;


use Psr\SimpleCache\CacheInterface;

class Consumer
{
    private $cacheQueue;
    private $handler;
    private $queueHandler;

    public function __construct(CacheInterface $cacheQueue, MessageHandler $handler, QueueHandler $queueHandler)
    {
        $this->cacheQueue = $cacheQueue;
        $this->handler = $handler;
        $this->queueHandler = $queueHandler;
    }

    public function process()
    {
        while ($this->queueHandler->isQueueActive()) {
            if (!$this->handler->handleMessage($this->popMessage())) {
                usleep(100000);
            }
        }
    }

    private function popMessage()
    {
        $queue = $this->cacheQueue->get('queuingMessages', []);
        $count = count($queue);
        if ($count > 0) {
            $message = array_pop($queue);
            $message['id'] = $count - 1;
            $this->cacheQueue->set('queuingMessages', $queue, 60);

            return $message;
        }

        return null;
    }
}
