<?php


namespace Async\Demo\Controllers\AsyncClients;


use Async\Demo\Controllers\ControllerInterface;
use Async\Demo\Utility\Timer;
use GuzzleHttp\Psr7\Request;
use Http\Client\HttpAsyncClient;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class GuzzleHttpCurl implements ControllerInterface
{
    private $guzzle;

    public function __construct(HttpAsyncClient $guzzle)
    {
        $this->guzzle = $guzzle;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $arguments = [])
    {
        $timer = new Timer();


        $firstPromise = $this->guzzle->sendAsyncRequest(
            new Request('GET', 'http://request.dev/animal/seconds/1')
        );
        $timer->check('first');
        $secondPromise = $this->guzzle->sendAsyncRequest(
            new Request('GET', 'http://request.dev/animal/seconds/2')
        );
        $timer->check('second');

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
