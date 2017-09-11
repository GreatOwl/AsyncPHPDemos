<?php


namespace Async\Demo\Controllers\Requests;


use Async\Demo\Controllers\ControllerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Animals implements ControllerInterface
{
    private $method;

    public function __construct($method = 'sleep')
    {
        $this->method = $method;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $arguments = [])
    {
        $delay = $arguments['delay'];
        if ($this->method == 'sleep') {
            sleep($delay);
        } else {
            usleep($delay);
        }
        $body = $response->getBody();
        $body->write(json_encode($this->getAnimals($delay)));

        return $response;
    }

    public function getAnimals($delay)
    {
        return [
            [
                'name' => 'cat',
                'id' => '3hu327',
                'delay' => $delay,
            ],
            [
                'name' => 'fish',
                'id' => '3hu328',
                'delay' => $delay,
            ],
            [
                'name' => 'bird',
                'id' => '3hu329',
                'delay' => $delay,
            ],
            [
                'name' => 'dog',
                'id' => '4hu328',
                'delay' => $delay,
            ],
            [
                'name' => 'insect',
                'id' => '4hu329',
                'delay' => $delay,
            ],
            [
                'name' => 'worm',
                'id' => '4hb329',
                'delay' => $delay,
            ],
        ];
    }
}
