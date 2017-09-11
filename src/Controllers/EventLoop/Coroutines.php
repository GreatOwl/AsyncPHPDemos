<?php


namespace Async\Demo\Controllers\EventLoop;


use Amp\Delayed;
use Amp\Promise;
use Amp\Success;
use Async\Demo\Controllers\ControllerInterface;
use Async\Demo\Utility\Timer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use function Amp\call;

class Coroutines implements ControllerInterface
{
    private $done = false;

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $arguments = [])
    {
        $timer = new Timer();

        $timer->check('01.start');
        $result = $timer->checkPoint($this->getAnimal($timer), '11.getAnimalReturns');

        $body = $response->getBody();
        $body->write(
            json_encode(
                [
                    'results' => $timer->checkPoint(Promise\wait($result), '12.getFinalResult'),
                    'timer' => $timer->toArray(),
                ]
            )
        );

        return $response;
    }

    public function getAnimal(Timer $timer)
    {
        return call(
            function () use ($timer) {
                $timer->check('02.getAnimalStarted');

                $firstAnimalPromise = $this->getAnimalSeconds(1, $timer, '03');
                $secondAnimalPromise = $this->getAnimalSeconds(2, $timer, '04');

                //AMP implementation requires yielding promises
                // for co-routine cross communication
                yield new Success();
                $timer->check('05.firstStartWaiting');
                $firstResults = $timer->checkPoint(Promise\wait($firstAnimalPromise), '06.firstFinishWaiting');
                yield new Success();
                $timer->check('07.secondStartWaiting');
                $secondResults = $timer->checkPoint(Promise\wait($secondAnimalPromise), '08.secondFinishWaiting');

                $timer->check('09.waitedSecondResult');
                $this->done = true;

                return $timer->checkPoint([$firstResults, $secondResults], '10.resultsWritten');
            }
        );
    }

    public function getAnimalSeconds($delay, Timer $timer, $key)
    {
        return call(
            function () use ($delay, $timer, $key) {
                //Arbitrarily delaying to simulate some, time expensive,
                // but non blocking operation.
                $timer->check("$key.1.animalStarted");
                yield new Delayed($delay * 1000);

                return $timer->checkPoint(
                    [
                        'name' => 'cat',
                        'id' => '3hu328',
                        'delay' => $delay,
                    ],
                    "$key.2.animalFinished"
                );
            }
        );
    }
}
