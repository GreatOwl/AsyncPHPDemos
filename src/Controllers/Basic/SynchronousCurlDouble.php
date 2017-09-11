<?php


namespace Async\Demo\Controllers\Basic;


use Async\Demo\Controllers\ControllerInterface;
use Async\Demo\Utility\Timer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class SynchronousCurlDouble implements ControllerInterface
{

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $arguments = [])
    {
        $timer = new Timer();
        $first = curl_init();

        //Set up first handle - same
        curl_setopt($first, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($first, CURLOPT_URL, 'http://request.dev/animal/seconds/1');

        //make copy of first
        $second = curl_copy_handle($first);

        //start both executing and get their results
        $firstResult = $timer->checkPoint(json_decode(curl_exec($first), true), 'first');

        $secondResult = $timer->checkPoint(json_decode(curl_exec($second), true), 'second');

        $results = $timer->checkPoint([$firstResult, $secondResult], 'resultsWritten');

        $body = $response->getBody();
        $body->write(json_encode(['results' => $results, 'timer' => $timer->toArray()]));

        return $response;
    }
}
