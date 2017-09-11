<?php


namespace Async\Demo\Container;


use Async\Demo\Controllers\BlockingCode\Collections\AnimalsFactory;
use Async\Demo\Controllers\BlockingCode\Collections\Parser as animalParser;
use Async\Demo\Controllers\BlockingCode\Modeling\AnimalFactory;
use Async\Demo\Controllers\BlockingCode\Modeling\ExtractionLoader;
use Async\Demo\Controllers\BlockingCode\Modeling\Parser;
use Async\Demo\Queueing\Consumer;
use Async\Demo\Queueing\MessageHandler;
use Async\Demo\Queueing\Producer;
use Async\Demo\Queueing\QueueHandler;
use Async\Demo\Queueing\Status;
use Cache\Adapter\Predis\PredisCachePool;
use Cache\Bridge\SimpleCache\SimpleCacheBridge;
use GuzzleHttp\Client as Guzzle;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;
use Http\Adapter\React\Client;
use Http\Discovery\MessageFactoryDiscovery;
use React\EventLoop\Factory;
use Slim\App;
use Slim\Container;

class Main
{
    private $container;

    public function __construct()
    {
        $this->container = new Container();
    }

    public function getContainer()
    {
        $this->container['guzzleHttpCurlClient'] = function (Container $container) {
            return new GuzzleAdapter(new Guzzle());
        };

        $this->container['react'] = function (Container $container) {
            $eventLoop = Factory::create();
            $dnsResolverFactory = new \React\Dns\Resolver\Factory();
            $dnsResolver = $dnsResolverFactory->createCached('127.0.0.11', $eventLoop);
            $factory = new \React\HttpClient\Factory();
            $reactHttp = $factory->create($eventLoop, $dnsResolver);

            return new Client(MessageFactoryDiscovery::find(), $eventLoop, $reactHttp);
        };

        $this->container['reactHttpSocketClient'] = function (Container $container) {
//            return $this->container->get('guzzleHttpCurlClient');
            return $this->container->get('react');
        };

        $this->container['animalFactory'] = function (Container $container) {
            return new AnimalFactory();
        };

        $this->container['animalsFactory'] = function (Container $container) {
            return new AnimalsFactory();
        };

        $this->container['animalParser'] = function (Container $container) {
            return new Parser();
        };

        $this->container['animalsParser'] = function (Container $container) {
            return new animalParser();
        };

        $this->container['extractionLoader'] = function (Container $container) {
            return new ExtractionLoader(
                $container->get('animalParser'),
                $container->get('animalFactory')
            );
        };

        $this->container['modelLoader'] = function (Container $container) {
            return new ExtractionLoader(
                $container->get('animalsParser'),
                $container->get('animalFactory')
            );
        };

        $this->container['collectionExtractionLoader'] = function (Container $container) {
            return new ExtractionLoader(
                $container->get('animalParser'),
                $container->get('animalsFactory'),
                $container->get('modelLoader')
            );
        };

        $this->container['predisClient'] = function (Container $container) {
            return new \Predis\Client('redis:6379');
        };

        $this->container['cachePool'] = function (Container $container) {
            return new PredisCachePool($container->get('predisClient'));
        };

        $this->container['simpleCache'] = function (Container $container) {
            return new SimpleCacheBridge($container->get('cachePool'));
        };

        $this->container['queueHandler'] = function (Container $container) {
            return new QueueHandler($container->get('simpleCache'));
        };

        $this->container['messageHandler'] = function (Container $container) {
            return new MessageHandler($container->get('simpleCache'));
        };

        $this->container['consumer'] = function (Container $container) {
            return new Consumer(
                $container->get('simpleCache'),
                $container->get('messageHandler'),
                $container->get('queueHandler')
            );
        };

        $this->container['producer'] = function (Container $container) {
            return new Producer($container->get('simpleCache'));
        };

        $this->container['status'] = function (Container $container) {
            return new Status($container->get('simpleCache'));
        };

        Controllers::loadContainer($this->container);

        return $this->container;
    }

    public function loadContainer(App $slim)
    {
        $container = $slim->getContainer();
        Routes::loadRoutes($container);
    }
}
