<?php

use App\Factories\ContainerFactory;
use DI\Bridge\Slim\Bridge;

require __DIR__ . '/../vendor/autoload.php';

// Instantiate PHP-DI ContainerBuilder
$container = ContainerFactory::createInstance();

// Instantiate App
$app = Bridge::create($container);

// Register middleware
$middleware = require __DIR__ . '/../app/middleware.php';
$middleware($app, $container);

// Register routes
$routes = require __DIR__ . '/../app/routes.php';
$routes($app);

$app->run();
