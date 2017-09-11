<?php


namespace Async\Demo\Controllers\Queueing;


use Amp\Delayed;
use Async\Demo\Controllers\ControllerInterface;
use Async\Demo\Utility\Timer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use function Amp\call;
use function Amp\Promise\wait;

class StartConsumers implements ControllerInterface
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $arguments = [])
    {
        $consumers = $this->startConsumers($arguments['consumers']);

        $response->getBody()->write(json_encode(wait($consumers)));

        return $response;
    }

    public function startConsumer($i, Timer $timer)
    {
        return call(
            function () use ($i, $timer) {
                $root = rtrim(__DIR__, '\/') . '/../../../public/activateQueue.php';
                $timer->check("startingConsumer$i");
                pclose($handle = popen('php ' . $root . ' &', 'r'));
                yield new Delayed(30);

                return $handle;
            }
        );
    }

    public function startConsumers($consumers)
    {
        return call(
            function () use ($consumers) {
                $waiting = [];
                $timer = new Timer();
                for ($i = 0; $i < $consumers; $i++) {
                    $consumer = $this->startConsumer($i, $timer);
                    $waiting[] = $consumer;
                    yield $consumer;
                }

                $handles = [];
                foreach ($waiting as $consumer => $handle) {
                    $handle = wait($handle);
                    $handles["consumer: $consumer"] = "consumer $handle started";
                }

                return [$handles, $timer->toArray()];
            }
        );
    }
}
