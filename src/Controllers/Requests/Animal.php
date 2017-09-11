<?php


namespace Async\Demo\Controllers\Requests;


use Async\Demo\Controllers\ControllerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Animal implements ControllerInterface
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
        $body->write(json_encode(self::getCat($delay)));

        return $response;
    }

    public static function getCat($delay)
    {
        return [
            'name' => 'cat',
            'id' => '3hu328',
            'delay' => $delay,
        ];
    }
}
