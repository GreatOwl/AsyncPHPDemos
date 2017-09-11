<?php


namespace Async\Demo\Container;


use Async\Demo\Controllers\AsyncClients\GuzzleHttpCurl;
use Async\Demo\Controllers\AsyncClients\MixedHttpClient;
use Async\Demo\Controllers\AsyncClients\ReactHttpSocket;
use Async\Demo\Controllers\Basic\AsynchronousCurl;
use Async\Demo\Controllers\Basic\LazyLoadCurl;
use Async\Demo\Controllers\Basic\SynchronousCurlDouble;
use Async\Demo\Controllers\Basic\SynchronousCurlSingle;
use Async\Demo\Controllers\BlockingCode\AsyncModel;
use Async\Demo\Controllers\BlockingCode\AsyncModelBlocking;
use Async\Demo\Controllers\BlockingCode\AsyncModelLazyLoading;
use Async\Demo\Controllers\BlockingCode\CollectionBlocking;
use Async\Demo\Controllers\BlockingCode\CollectionNonBlocking;
use Async\Demo\Controllers\BlockingCode\ProceduralBlocking;
use Async\Demo\Controllers\EventLoop\Coroutines;
use Async\Demo\Controllers\Queueing\AddMessages;
use Async\Demo\Controllers\Queueing\StartConsumers;
use Async\Demo\Controllers\Queueing\Status;
use Async\Demo\Controllers\Queueing\StopConsumers;
use Async\Demo\Controllers\Requests\Animal;
use Async\Demo\Controllers\Requests\Animals;
use Async\Demo\Midware\ContentType;
use Slim\Container;

class Controllers
{
    public static function loadContainer(Container $container)
    {
        $container['contentType'] = function (Container $container) {
            return new ContentType();
        };

        $container['demo1'] = function (Container $container) {
            return new SynchronousCurlSingle();
        };

        $container['demo2'] = function (Container $container) {
            return new LazyLoadCurl();
        };

        $container['demo2.9'] = function (Container $container) {
            return new SynchronousCurlDouble();
        };

        $container['demo3'] = function (Container $container) {
            return new AsynchronousCurl();
        };

        $container['demo4'] = function (Container $container) {
            return new GuzzleHttpCurl($container->get('guzzleHttpCurlClient'));
        };

        $container['demo5'] = function (Container $container) {
            return new ReactHttpSocket($container->get('reactHttpSocketClient'));
        };

        $container['demo6'] = function (Container $container) {
            return new MixedHttpClient(
                $container->get('guzzleHttpCurlClient'),
                $container->get('reactHttpSocketClient')
            );
        };

        $container['demo7'] = function (Container $container) {
            return new ProceduralBlocking($container->get('reactHttpSocketClient'));
        };

        $container['demo8'] = function (Container $container) {
            return new AsyncModel(
                $container->get('reactHttpSocketClient'),
                $container->get('extractionLoader')
            );
        };

        $container['demo8.1'] = function (Container $container) {
            return new AsyncModelBlocking(
                $container->get('reactHttpSocketClient'),
                $container->get('extractionLoader')
            );
        };

        $container['demo8.2'] = function (Container $container) {
            return new AsyncModelLazyLoading(
                $container->get('reactHttpSocketClient'),
                $container->get('extractionLoader')
            );
        };

        $container['demo9'] = function (Container $container) {
            return new CollectionBlocking(
                $container->get('reactHttpSocketClient'),
                $container->get('collectionExtractionLoader')
            );
        };

        $container['demo10'] = function (Container $container) {
            return new CollectionNonBlocking(
                $container->get('reactHttpSocketClient'),
                $container->get('collectionExtractionLoader')
            );
        };

        $container['startConsumers'] = function (Container $container) {
            return new StartConsumers();
        };

        $container['stopConsumers'] = function (Container $container) {
            return new StopConsumers($container->get('queueHandler'));
        };

        $container['statusConsumers'] = function (Container $container) {
            return new Status(
                $container->get('queueHandler'),
                $container->get('status'),
                $container->get('simpleCache')
            );
        };

        $container['coroutines'] = function (Container $container) {
            return new Coroutines();
        };

        $container['addMessages'] = function (Container $container) {
            return new AddMessages($container->get('producer'));
        };

        $container['animal'] = function (Container $container) {
            return new Animal();
        };
        $container['animals'] = function (Container $container) {
            return new Animals();
        };
        $container['fastAnimal'] = function (Container $container) {
            return new Animal('usleep');
        };
        $container['fastAnimals'] = function (Container $container) {
            return new Animals('usleep');
        };
    }
}
