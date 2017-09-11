<?php


namespace Async\Demo\Controllers\BlockingCode;


use Async\Demo\Controllers\BlockingCode\Modeling\Animal;
use Async\Demo\Controllers\BlockingCode\Modeling\ExtractionLoader;
use Async\Demo\Controllers\ControllerInterface;
use Async\Demo\Utility\Timer;
use GuzzleHttp\Psr7\Request;
use Http\Client\HttpAsyncClient;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AsyncModelLazyLoading implements ControllerInterface
{
    private $asyncClient;
    private $dataContainerLoader;

    public function __construct(HttpAsyncClient $asyncClient, ExtractionLoader $dataContainerLoader)
    {
        $this->asyncClient = $asyncClient;
        $this->dataContainerLoader = $dataContainerLoader;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $arguments = [])
    {
        $timer = new Timer();
        $animalRequest = new Request('GET', 'http://request.dev/animal/seconds/1');

        $firstAnimal = $timer->checkPoint($this->getModel($animalRequest), 'first');
        $firstResult = function () use ($timer, $firstAnimal) {
            return $timer->checkPoint($firstAnimal->toArray(), 'firstRead');
        };

        $secondAnimal = $timer->checkPoint($this->getModel($animalRequest), 'second');
        $secondResult = function () use ($timer, $secondAnimal) {
            return $timer->checkPoint($secondAnimal->toArray(), 'secondRead');
        };

        $results = [$firstResult(), $secondResult()];
        $responseData = $timer->checkPoint(['results' => $results], 'resultsWritten');
        $responseData['timer'] = $timer->toArray();

        $body = $response->getBody();
        $body->write(json_encode($responseData));

        return $response;
    }

    public function getModel(RequestInterface $request): Animal
    {
        return $this->dataContainerLoader->loadModel(
            $this->asyncClient->sendAsyncRequest($request),
            [
                $this->getModelHandler(),
            ]
        );
    }

    public function getModelHandler()
    {
        return function (ResponseInterface $response) {
            if ($response->getStatusCode() != 200) {
                throw new \Exception('uh oh');
            }

            return $this->dataContainerLoader->extract($response->getBody()->getContents());
        };
    }
}
