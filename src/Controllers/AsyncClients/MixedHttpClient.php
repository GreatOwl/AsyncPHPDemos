<?php


namespace Async\Demo\Controllers\AsyncClients;


use Async\Demo\Controllers\ControllerInterface;
use Async\Demo\Utility\Timer;
use GuzzleHttp\Psr7\Request;
use Http\Client\HttpAsyncClient;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class MixedHttpClient implements ControllerInterface
{
    private $guzzle;
    private $react;

    public function __construct(HttpAsyncClient $guzzle, HttpAsyncClient $react)
    {
        $this->guzzle = $guzzle;
        $this->react = $react;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $arguments = [])
    {
        $timer = new Timer();

        $animalRequest = new Request('GET', 'http://request.dev/animal/seconds/1');

        $firstPromise = $timer->checkPoint($this->guzzle->sendAsyncRequest($animalRequest), 'first');
        $secondPromise = $timer->checkPoint($this->react->sendAsyncRequest($animalRequest), 'second');

        /** @var ResponseInterface $firstResponse */
        $firstResponse = $firstPromise->wait();
        $firstResult = json_decode($firstResponse->getBody()->getContents(), true);
        $timer->check('firstRead');

        /** @var ResponseInterface $secondResponse */
        $secondResponse = $secondPromise->wait();
        $secondResult = json_decode($secondResponse->getBody()->getContents(), true);
        $timer->check('secondRead');

        $body = $response->getBody();
        $results = $timer->checkPoint([$firstResult, $secondResult], 'resultsWritten');
        $body->write(json_encode(['results' => $results, 'timer' => $timer->toArray()]));

        return $response;
    }
}
