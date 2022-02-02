<?php

use DI\Bridge\Slim\Bridge;
use DI\ContainerBuilder;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

// Instantiate PHP-DI ContainerBuilder
$containerBuilder = new ContainerBuilder();

// Set up settings
$settings = require __DIR__ . '/../app/settings.php';
$settings($containerBuilder);

// Set up dependencies
$dependencies = require __DIR__ . '/../app/dependencies.php';
$dependencies($containerBuilder);

// Set up repositories
/*$repositories = require __DIR__ . '/../app/repositories.php';
$repositories($containerBuilder);*/

//if ($containerBuilder->get('setting')['debug']) { // Should be set to true in production
//    $containerBuilder->enableCompilation(__DIR__ . '/../var/cache');
//}

// Build PHP-DI Container instance
$container = $containerBuilder->build();

// Instantiate the app
//AppFactory::setContainer($container);

// Create Request object from globals
/*$serverRequestCreator = ServerRequestCreatorFactory::create();
$request = $serverRequestCreator->createServerRequestFromGlobals();*/

//$app = AppFactory::create();
$app = Bridge::create($container);

// Register routes
$routes = require __DIR__ . '/../app/routes.php';
$routes($app);

// Register middleware
$middleware = require __DIR__ . '/../app/middleware.php';
$middleware($app);

$app->run();
