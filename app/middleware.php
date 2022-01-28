<?php
declare(strict_types=1);

use App\Handlers\HttpErrorHandler;
use App\Handlers\ShutdownHandler;
use Middlewares\Whoops;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;
use Slim\Middleware\Session;

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
        //$logger->error($exception->getMessage());

        $container = $app->getContainer();
        $response = $app->getResponseFactory()->createResponse();

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
    };

    // Create Shutdown Handler
    //$shutdownHandler = new ShutdownHandler($request, $errorHandler, true);
    //register_shutdown_function($shutdownHandler);

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
    if ($app->getContainer()->get('setting')['debug']) {
        $app->add(new Whoops());
    } else {
        $errorMiddleware = $app->addErrorMiddleware(true, true, true);
        $errorMiddleware->setDefaultErrorHandler($errorHandler);
    }
};
