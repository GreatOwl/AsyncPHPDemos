<?php


namespace Async\Demo\Controllers;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface ControllerInterface
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $arguments = []);
}
