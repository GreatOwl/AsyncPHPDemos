<?php


namespace Async\Demo\Controllers\BlockingCode;


use Async\Demo\Controllers\BlockingCode\Collections\Collection;
use Async\Demo\Controllers\BlockingCode\Modeling\Animal;
use Async\Demo\Controllers\BlockingCode\Modeling\ExtractionLoader;
use Async\Demo\Controllers\ControllerInterface;
use Async\Demo\Utility\Timer;
use GuzzleHttp\Psr7\Request;
use Http\Client\HttpAsyncClient;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CollectionBlocking implements ControllerInterface
{
    private $asyncClient;
    private $collectionLoader;

    public function __construct(HttpAsyncClient $asyncClient, ExtractionLoader $collectionLoader)
    {
        $this->asyncClient = $asyncClient;
        $this->collectionLoader = $collectionLoader;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $arguments = [])
    {
        $timer = new Timer();

        $animalRequest = new Request('GET', 'http://request.dev/animals/seconds/1');

        $firstAnimals = $timer->checkPoint($this->getCollection($animalRequest), 'first');
        $firstAnimals = $firstAnimals->filter(
            function (Animal $animal) {
                return $animal->getName() == 'cat';
            }
        );
        $timer->check('firstFilter');

        $secondAnimals = $timer->checkPoint($this->getCollection($animalRequest), 'second');
        $secondAnimals = $secondAnimals->filter(
            function (Animal $animal) {
                return $animal->getName() == 'dog';
            }
        );
        $timer->check('secondFilter');


        $firstAnimals = $timer->checkPoint($firstAnimals->merge($secondAnimals), 'merge');

        $results = [];
        /** @var Animal $animal */
        foreach ($firstAnimals as $animal) {
            $results[] = $animal->toArray();
        }

        $responseData = $timer->checkPoint(['results' => $results], 'resultsWritten');

        $responseData['timer'] = $timer->toArray();

        $body = $response->getBody();
        $body->write(json_encode($responseData));

        return $response;
    }

    public function getCollection(RequestInterface $request): Collection
    {
        return $this->collectionLoader->loadModel(
            $this->asyncClient->sendAsyncRequest($request),
            [
                $this->getCollectionHandler(),
            ]
        );
    }

    private function getCollectionHandler(): callable
    {
        return function (ResponseInterface $response) {
            if ($response->getStatusCode() != 200) {
                throw new \Exception('uh oh');
            }

            return $this->collectionLoader->extract($response->getBody()->getContents());
        };
    }
}
