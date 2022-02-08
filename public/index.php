<?php

use DI\Bridge\Slim\Bridge;

require __DIR__ . '/../vendor/autoload.php';

// Instantiate PHP-DI ContainerBuilder
$container = require __DIR__ . '/../app/container.php';

// Instantiate App
$app = Bridge::create($container);

// Register middleware
$middleware = require __DIR__ . '/../app/middleware.php';
$middleware($app, $container);

// Register routes
$routes = require __DIR__ . '/../app/routes.php';
$routes($app);

$app->run();
