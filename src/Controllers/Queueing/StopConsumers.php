<?php


namespace Async\Demo\Controllers\Queueing;


use Async\Demo\Controllers\ControllerInterface;
use Async\Demo\Queueing\QueueHandler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class StopConsumers implements ControllerInterface
{
    private $consumer;

    public function __construct(QueueHandler $consumer)
    {
        $this->consumer = $consumer;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $arguments = [])
    {
        $this->consumer->deactivateQueue();

        $body = $response->getBody();
        $body->write(json_encode(['consumers stopped' => !$this->consumer->isQueueActive()]));

        return $response;
    }
}
