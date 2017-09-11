<?php


namespace Async\Demo\Controllers\Basic;


use Async\Demo\Controllers\ControllerInterface;
use Async\Demo\Utility\Timer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class LazyLoadCurl implements ControllerInterface
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

        //lazy load the execution of both before getting their results
        $firstResultCallback = $this->lazyGetResult($first, $timer, 'first');

        $secondResultCallback = $this->lazyGetResult($second, $timer, 'second');

        //check trivial condition before making second request
        $secondResult = $secondResultCallback();
        $results = [$secondResult];
        if ($secondResult['name'] == 'dog') {
            $results[] = $firstResultCallback();
        }
        $timer->check('resultsWritten');

        $body = $response->getBody();
        $body->write(json_encode(['results' => $results, 'timer' => $timer->toArray()]));

        return $response;
    }

    public function lazyGetResult($handle, Timer $timer, $timerKey)
    {
        return function () use ($handle, $timer, $timerKey) {
            //start executing and get the results;
            return $timer->checkPoint(json_decode(curl_exec($handle), true), $timerKey);
        };
    }
}
