<?php

$root = __DIR__ . '/..';
require_once $root . '/vendor/autoload.php';


$containerBuilder = new \Async\Demo\Container\Main();
$container = $containerBuilder->getContainer();
$app = new \Slim\App($container);

$container['slim'] = function (\Slim\Container $container) use ($app){
    return $app;
};
$containerBuilder->loadContainer($app);
$loadedContainer = $app->getContainer();
/** @var \Async\Demo\Queueing\Consumer $consumer */
$consumer = $loadedContainer->get('consumer');
/** @var \Async\Demo\Queueing\QueueHandler $queueHandler */
$queueHandler = $loadedContainer->get('queueHandler');

$queueHandler->activateQueue();
$consumer->process();
echo "queue stopped\n";
