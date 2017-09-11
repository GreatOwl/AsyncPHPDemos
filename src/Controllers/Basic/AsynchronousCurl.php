<?php


namespace Async\Demo\Controllers\Basic;


use Async\Demo\Controllers\ControllerInterface;
use Async\Demo\Utility\Timer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AsynchronousCurl implements ControllerInterface
{

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $arguments = [])
    {
        $timer = new Timer();
        $multiHandle = curl_multi_init();

        //Set up first handle - same
        $first = curl_init();
        curl_setopt($first, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($first, CURLOPT_URL, 'http://request.dev/animal/seconds/1');

        //make copy of first
        $second = curl_copy_handle($first);

        //add to multi handler
        curl_multi_add_handle($multiHandle, $first);
        curl_multi_add_handle($multiHandle, $second);

        //start both executing
        while (curl_multi_exec($multiHandle, $active) === CURLM_CALL_MULTI_PERFORM || $active);

        //get their results
        $firstResult = $timer->checkPoint(json_decode(curl_multi_getcontent($first), true), 'first');

        $secondResult = $timer->checkPoint(json_decode(curl_multi_getcontent($second), true), 'second');

        $body = $response->getBody();
        $results = $timer->checkPoint([$firstResult, $secondResult], 'resultsWritten');
        $body->write(json_encode(['results' => $results, 'timer' => $timer->toArray()]));

        return $response;
    }
}
