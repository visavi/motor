<?php

namespace App\Handlers;

use App\Services\View;
use DomainException;
use Fig\Http\Message\StatusCodeInterface;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpException;
use Slim\Interfaces\ErrorHandlerInterface;
use Slim\Psr7\Response;
use Throwable;
use Whoops\Handler\JsonResponseHandler;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;
use Whoops\Util\Misc;

/**
 * Default Error Renderer.
 */
final class DefaultErrorHandler implements ErrorHandlerInterface
{
    public function __construct(
        protected View $view,
        protected LoggerInterface $logger,
    ) {}

    /**
     * Invoke.
     *
     * @param ServerRequestInterface $request The request
     * @param Throwable $exception The exception
     * @param bool $displayErrorDetails Show error details
     * @param bool $logErrors Log errors
     * @param bool $logErrorDetails Log error details
     *
     * @return ResponseInterface The response
     */
    public function __invoke(
        ServerRequestInterface $request,
        Throwable $exception,
        bool $displayErrorDetails,
        bool $logErrors,
        bool $logErrorDetails
    ): ResponseInterface {
        $response = new Response();
        $code     = $this->getHttpStatusCode($exception);

        if (strtolower($request->getHeaderLine('X-Requested-With')) === 'xmlhttprequest') {
            $error = [
                'error' => [
                    'code'    => $code,
                    'message' => $this->getErrorMessage($exception),
                ]
            ];
            $response->getBody()->write((string) json_encode($error));

            return $response->withStatus($code)->withHeader('Content-Type', 'application/json');
        }

        if (setting('logError')) {
            $error = $this->getErrorDetails($exception);
            $error['method'] = $request->getMethod();
            $error['url']    = $request->getUri()->getPath();

            $this->logger->error($exception->getMessage(), $error);
        }

        if (
            class_exists(Run::class)
            && ! $exception instanceof HttpException
        ) {
            $handler = Misc::isAjaxRequest() ?
                new JsonResponseHandler() :
                new PrettyPageHandler();

            $whoops = new Run();
            $whoops->pushHandler($handler);

            $response->getBody()->write($whoops->handleException($exception));

            return $response;
        }

        $view = $this->view->exists('errors/' . $code) ? $code : 'default';
        $response = $this->view->render(
            $response,
            'errors/' . $view,
            ['message' => $this->getErrorMessage($exception)]
        );

        return $response->withStatus($code);
    }

    /**
     * Get http status code.
     *
     * @return int The http code
     */
    private function getHttpStatusCode(Throwable $exception): int
    {
        // Detect status code
        $statusCode = StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR;

        if ($exception instanceof HttpException) {
            $statusCode = $exception->getCode();
        }

        if (
            $exception instanceof DomainException
            || $exception instanceof InvalidArgumentException
        ) {
            $statusCode = StatusCodeInterface::STATUS_BAD_REQUEST;
        }

        $file = basename($exception->getFile());
        if ($file === 'CallableResolver.php') {
            $statusCode = StatusCodeInterface::STATUS_NOT_FOUND;
        }

        return $statusCode;
    }

    /**
     * Get error message.
     *
     * @param Throwable $exception The error
     *
     * @return array The error details
     */
    private function getErrorDetails(Throwable $exception): array
    {
        if (setting('logErrorDetails')) {
            return [
                'message'  => $exception->getMessage(),
                'code'     => $exception->getCode(),
                'file'     => $exception->getFile(),
                'line'     => $exception->getLine(),
                'previous' => $exception->getPrevious(),
                'trace'    => $exception->getTrace(),
            ];
        }

        return [
            'message' => $exception->getMessage(),
        ];
    }

    /**
     * Get error message.
     *
     * @param Throwable $exception The error
     *
     * @return string The message
     */
    private function getErrorMessage(Throwable $exception): string
    {
        $errorMessage = '500 Internal Server Error';

        if ($exception->getCode() === 403) {
            $errorMessage = '403 Access denied';
        } elseif ($exception->getCode() === 404) {
            $errorMessage = '404 Not Found';
        } elseif ($exception->getCode() === 405) {
            $errorMessage = '405 Method Not Allowed';
        } elseif ($exception->getCode() >= 400 && $exception->getCode() <= 499) {
            $errorMessage = sprintf('%s Error', $exception->getCode());
        }

        if (setting('displayErrorDetails')) {
            $errorMessage = $exception->getMessage();
        }

        return $errorMessage;
    }
}
