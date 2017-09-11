<?php


namespace Async\Demo\Container;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;
use Slim\Container;

class Routes
{
    public static function loadRoutes(Container $container)
    {
        /** @var App $slim */
        $slim = $container->get('slim');

        // Basics
        $slim->get('/synchronousSingleCurl', self::buildRoute($container, 'demo1'))->add(
            $container->get('contentType')
        );
        $slim->get('/lazyLoadSingleCurl', self::buildRoute($container, 'demo2'))->add($container->get('contentType'));
        $slim->get('/synchronousDoubleCurl', self::buildRoute($container, 'demo2.9'))->add(
            $container->get('contentType')
        );
        $slim->get('/asynchronousCurl', self::buildRoute($container, 'demo3'))->add($container->get('contentType'));

        // Client types
        $slim->get('/guzzleHttpCurl', self::buildRoute($container, 'demo4'))->add($container->get('contentType'));
        $slim->get('/reactHttpSocket', self::buildRoute($container, 'demo5'))->add($container->get('contentType'));
        $slim->get('/mixedClient', self::buildRoute($container, 'demo6'))->add($container->get('contentType'));

        // Blocking code
        $slim->get('/proceduralBlocking', self::buildRoute($container, 'demo7'))->add($container->get('contentType'));
        $slim->get('/asyncModel', self::buildRoute($container, 'demo8'))->add($container->get('contentType'));
        $slim->get('/asyncModelBlocking', self::buildRoute($container, 'demo8.1'))->add($container->get('contentType'));
        $slim->get('/asyncModelLazyLoading', self::buildRoute($container, 'demo8.2'))->add(
            $container->get('contentType')
        );
        $slim->get('/collectionBlocking', self::buildRoute($container, 'demo9'))->add($container->get('contentType'));
        $slim->get('/collectionNonBlocking', self::buildRoute($container, 'demo10'))->add(
            $container->get('contentType')
        );

        // Event Loop
        $slim->get('/coroutines', self::buildRoute($container, 'coroutines'))->add($container->get('contentType'));

        // Queuing
        $slim->get(
            '/startConsumers/{consumers:[\d]{1,2}$|[1]{1}[0,1,2]{1}[\d]{1}}',
            self::buildRoute($container, 'startConsumers')
        )->add($container->get('contentType'));
        $slim->get('/stopConsumers', self::buildRoute($container, 'stopConsumers'))->add(
            $container->get('contentType')
        );
        $slim->get('/statusConsumers', self::buildRoute($container, 'statusConsumers'))->add(
            $container->get('contentType')
        );
        $slim->get('/addMessages/{messages:[\d]{1,4}}', self::buildRoute($container, 'addMessages'))->add(
            $container->get('contentType')
        );

        $slim->get('/animal/seconds/{delay}', self::buildRoute($container, 'animal'))->add(
            $container->get('contentType')
        );
        $slim->get('/animal/micro/{delay}', self::buildRoute($container, 'fastAnimal'))->add(
            $container->get('contentType')
        );
        $slim->get('/animals/seconds/{delay}', self::buildRoute($container, 'animals'))->add(
            $container->get('contentType')
        );
        $slim->get('/animals/micro/{delay}', self::buildRoute($container, 'fastAnimals'))->add(
            $container->get('contentType')
        );
    }

    public static function buildRoute(Container $container, $controllerName)
    {
        return function (
            ServerRequestInterface $request,
            ResponseInterface $response,
            $arguments
        ) use (
            $container,
            $controllerName
        ) {
            return call_user_func(
                $container->get($controllerName),
                $request,
                $response,
                $arguments
            );
        };
    }
}
