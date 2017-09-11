<?php


namespace Async\Demo\Midware;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ContentType
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $next)
    {
        $next($request, $response);

        return $response->withHeader('Content-Type', 'application/json');
    }
}
