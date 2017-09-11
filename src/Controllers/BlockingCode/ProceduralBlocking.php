<?php


namespace Async\Demo\Controllers\BlockingCode;


use Async\Demo\Controllers\ControllerInterface;
use Async\Demo\Utility\Timer;
use GuzzleHttp\Psr7\Request;
use Http\Client\HttpAsyncClient;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ProceduralBlocking implements ControllerInterface
{
    private $asyncClient;

    public function __construct(HttpAsyncClient $asyncClient)
    {
        $this->asyncClient = $asyncClient;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $arguments = [])
    {
        $timer = new Timer();

        $animalRequest = new Request('GET', 'http://request.dev/animal/seconds/1');

        $firstPromise = $this->asyncClient->sendAsyncRequest($animalRequest);
        $timer->check('first');
        $firstResponse = $firstPromise->wait();
        $timer->check('firstRead');


        $secondPromise = $this->asyncClient->sendAsyncRequest($animalRequest);
        $timer->check('second');
        $secondResponse = $secondPromise->wait();
        $timer->check('secondRead');


        //Render the results
        $firstResult = json_decode($firstResponse->getBody()->getContents(), true);
        $secondResult = json_decode($secondResponse->getBody()->getContents(), true);

        $body = $response->getBody();
        $results = [$firstResult, $secondResult];
        $timer->check('resultsWritten');
        $body->write(json_encode(['results' => $results, 'timer' => $timer->toArray()]));

        return $response;
    }
}
