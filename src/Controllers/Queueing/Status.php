<?php


namespace Async\Demo\Controllers\Queueing;


use Async\Demo\Controllers\ControllerInterface;
use Async\Demo\Queueing\QueueHandler;
use Async\Demo\Queueing\Status as QueueStatus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\SimpleCache\CacheInterface;

class Status implements ControllerInterface
{

    private $queueHandler;
    private $status;
    private $cache;

    public function __construct(QueueHandler $queueHandler, QueueStatus $status, CacheInterface $cache)
    {
        $this->queueHandler = $queueHandler;
        $this->status = $status;
        $this->cache = $cache;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $arguments = [])
    {
        $body = $response->getBody();
        $messages = $this->status->getMessages();
        $status = $this->status->getStatus();

        $started = is_null($status['started']) ? [] : $status['started'];
        $complete = is_null($status['complete']) ? [] : $status['complete'];

        list($waiting, $startedCount, $completeCount, $original) = $this->resolveCounts($messages, $started, $complete);
        $body->write(
            json_encode(
                [
                    'consumers active' => $this->queueHandler->isQueueActive(),
                    'originalCount' => $original,
                    'waitingCount' => $waiting,
                    'startedCount' => $startedCount,
                    'completeCount' => $completeCount,
                    'total execution time' => $this->status->getExecutionTime(),
                    'waiting' => $messages,
                    'started' => $startedCount === 0 ? [] : $started,
                    'completed' => $complete,
                ]
            )
        );

        return $response;
    }

    private function resolveCounts($messages, $started, $complete)
    {
        // Yes this is cheating. I built my consumer poorly and as a result there are race conditions
        // To resolve this for the examples, I try to detect when they have occurred and clean them up at the end.
        $waiting = count($messages);
        $startedCount = count($started);
        $completeCount = count($complete);
        $original = (int)$this->cache->get('queuingOriginal', 0);
        if ($original == 0) {
            $original = $waiting;
        }

        if ($completeCount >= $original) {
            $completeCount = $original;
            $startedCount = 0;
        }

        $total = $startedCount + $completeCount + $waiting;
        if ($total >= $original) {
            $extra = $total - $original;
            $completeCount = $completeCount - $extra;
        }

        if ($total < $original) {
            $completeCount = $original;
            $startedCount = 0;
        }

        return [$waiting, $startedCount, $completeCount, $original];
    }
}
