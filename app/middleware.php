<?php

declare(strict_types=1);

use App\Handlers\DefaultErrorHandler;
use App\Middleware\CheckAccessMiddleware;
use App\Middleware\IpAddressMiddleware;
use App\Middleware\StartSessionMiddleware;
use App\Middleware\TrailingSlashMiddleware;
use App\Middleware\UserAuthMiddleware;
use App\Services\Setting;
use Psr\Log\LoggerInterface;
use Slim\App;
use Slim\Middleware\MethodOverrideMiddleware;

return function (App $app)
{
    /**
     * The routing middleware should be added earlier than the ErrorMiddleware
     * Otherwise exceptions thrown from it will not be handled by the middleware
     */
    $app->addRoutingMiddleware();

    // Add MethodOverride middleware
    $app->add(MethodOverrideMiddleware::class);

    // Trailing slash middleware
    $app->add(TrailingSlashMiddleware::class);

    // Check access middleware
    $app->add(CheckAccessMiddleware::class);

    // UserAuth middleware
    $app->add(UserAuthMiddleware::class);

    // Session middleware
    $app->add(StartSessionMiddleware::class);

    // Ip address middleware
    $app->add(IpAddressMiddleware::class);

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
    $setting = $app->getContainer()->get(Setting::class);

    $errorMiddleware = $app->addErrorMiddleware(
        $setting->get('displayErrorDetails'),
        $setting->get('logError'),
        $setting->get('logErrorDetails'),
        $app->getContainer()->get(LoggerInterface::class)
    );

    // Define Custom Error Handler
    $errorMiddleware->setDefaultErrorHandler(DefaultErrorHandler::class);
};
