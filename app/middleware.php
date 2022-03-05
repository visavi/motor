<?php

declare(strict_types=1);

use App\Handlers\HttpErrorHandler;
use App\Middleware\StartSession;
use App\Middleware\TrailingSlashMiddleware;
use App\Middleware\UserAuthMiddleware;
use Psr\Container\ContainerInterface;
use Slim\App;
use Slim\Middleware\MethodOverrideMiddleware;

return function (App $app, ContainerInterface $container) {
    /**
     * The routing middleware should be added earlier than the ErrorMiddleware
     * Otherwise exceptions thrown from it will not be handled by the middleware
     */
    $app->addRoutingMiddleware();

    // Add MethodOverride middleware
    $app->add(MethodOverrideMiddleware::class);

    // Trailing slash middleware
    $app->add(TrailingSlashMiddleware::class);

    // UserAuth middleware
    $app->add(UserAuthMiddleware::class);

    // Session middleware
    $app->add(StartSession::class);

    // Define Custom Error Handler
    $errorHandler = new HttpErrorHandler($app->getCallableResolver(), $app->getResponseFactory());

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
    $errorMiddleware->setDefaultErrorHandler($errorHandler);
};
