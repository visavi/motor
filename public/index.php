<?php

use App\Controllers\GuestbookController;
use App\Controllers\HomeController;
use App\Paginator;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Views\Twig;

require __DIR__ . '/../vendor/autoload.php';

// Instantiate PHP-DI ContainerBuilder
$containerBuilder = new ContainerBuilder();

if (false) { // Should be set to true in production
    $containerBuilder->enableCompilation(__DIR__ . '/../var/cache');
}

// Set up settings
/*$settings = require __DIR__ . '/../app/settings.php';
$settings($containerBuilder);*/

// Set up dependencies
/*$dependencies = require __DIR__ . '/../app/dependencies.php';
$dependencies($containerBuilder);*/

// Set up repositories
/*$repositories = require __DIR__ . '/../app/repositories.php';
$repositories($containerBuilder);*/

// Build PHP-DI Container instance
$container = $containerBuilder->build();

// Instantiate the app
AppFactory::setContainer($container);

// Set view in Container
$container->set('view', function() {
    return Twig::create(__DIR__ . '/../resources/views', [
        'cache'       => __DIR__ . '/../var/views',
        'auto_reload' => true,
        'debug'       => true,
    ]);
});

$container->set('paginator', function(ContainerInterface $container) {
    return new Paginator($container->get('view'));
});

// Create Request object from globals
/*$serverRequestCreator = ServerRequestCreatorFactory::create();
$request = $serverRequestCreator->createServerRequestFromGlobals();*/

//$app = AppFactory::create();
$app = \DI\Bridge\Slim\Bridge::create($container);
/**
 * The routing middleware should be added earlier than the ErrorMiddleware
 * Otherwise exceptions thrown from it will not be handled by the middleware
 */
$app->addRoutingMiddleware();

/**
 * Add Error Middleware
 *
 * @param bool                  $displayErrorDetails -> Should be set to false in production
 * @param bool                  $logErrors -> Parameter is passed to the default ErrorHandler
 * @param bool                  $logErrorDetails -> Display error details in error log
 * @param LoggerInterface|null  $logger -> Optional PSR-3 Logger
 *
 * Note: This middleware should be added last. It will not handle any exceptions/errors
 * for middleware added after it.
 */
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

$app->get('/', [HomeController::class, 'home']);

$app->group('/guestbook', function (RouteCollectorProxy $group) {
    $group->get('', [GuestbookController::class, 'index']);
    $group->post('/create', [GuestbookController::class, 'create']);
    $group->get('/{id:[0-9]+}/edit', [GuestbookController::class, 'edit']);
    $group->post('/{id:[0-9]+}/edit', [GuestbookController::class, 'store']);
    $group->get('/{id:[0-9]+}/delete', [GuestbookController::class, 'delete']);
});

$app->run();
