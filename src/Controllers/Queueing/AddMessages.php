<?php


namespace Async\Demo\Controllers\Queueing;


use Async\Demo\Controllers\ControllerInterface;
use Async\Demo\Queueing\Producer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AddMessages implements ControllerInterface
{
    private $producer;

    public function __construct(Producer $producer)
    {
        $this->producer = $producer;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $arguments = [])
    {
        $results = $this->producer->addMessages($arguments['messages']);
        $body = $response->getBody();
        $body->write(
            json_encode(
                [
                    'messages added' => $results[0],
                    'total delay' => $results[1],
                ]
            )
        );

        return $response;
    }
}
