<?php

declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface;
use Slim\App;
use Slim\Exception\HttpException;
use Slim\Middleware\Session;
use Whoops\Handler\JsonResponseHandler;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;
use Whoops\Util\Misc;

return function (App $app) {
    /**
     * The routing middleware should be added earlier than the ErrorMiddleware
     * Otherwise exceptions thrown from it will not be handled by the middleware
     */
    $app->addRoutingMiddleware();

    // Session middleware
    $app->add(
        new Session([
            'name'        => 'motor_session',
            'lifetime'    => '1 hour',
            'httponly'    => true,
            'autorefresh' => true,
        ])
    );

    // Define Custom Error Handler
    $errorHandler = function (
        ServerRequestInterface $request,
        Throwable $exception,
    ) use ($app) {
        $container = $app->getContainer();
        $response = $app->getResponseFactory()->createResponse();

        if (
            $exception instanceof HttpException
            || ! $container->get('setting')['debug']
        ) {
            $code = $exception->getCode();

            if (! $container->get('view')->exists('errors/' . $code)) {
                $code = 500;
            }

            $response = $container->get('view')->render(
                $response,
                'errors/' . $code,
                ['message' => $exception->getMessage()]
            );

            return $response->withStatus($code);
        }

        if ($container->get('setting')['debug']) {
            $handler = Misc::isAjaxRequest() ?
                new JsonResponseHandler() :
                new PrettyPageHandler();

            $whoops = new Run();
            $whoops->pushHandler($handler);

            $response->getBody()->write($whoops->handleException($exception));
        }

        return $response;
    };

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
