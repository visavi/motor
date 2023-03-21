<?php

declare(strict_types=1);

namespace App\Handlers;

use App\Services\View;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpException;
use Slim\Handlers\ErrorHandler as SlimErrorHandler;
use Whoops\Handler\JsonResponseHandler;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;
use Whoops\Util\Misc;

class HttpErrorHandler extends SlimErrorHandler
{
    protected function respond(): Response
    {
        $response = $this->responseFactory->createResponse();

        if ($this->exception instanceof HttpException || ! setting('displayErrorDetails')) {
            $code = $this->statusCode;

            if (strtolower($this->request->getHeaderLine('X-Requested-With')) === 'xmlhttprequest') {
                $error = [
                    'error' => [
                        'code'    => $code,
                        'message' => $this->exception->getMessage(),
                    ]
                ];
                $response->getBody()->write((string) json_encode($error));

                return $response->withStatus($code)->withHeader('Content-Type', 'application/json');
            }

            if (! app(View::class)->exists('errors/' . $code)) {
                $code = 500;
            }

            $response = app(View::class)->render(
                $response,
                'errors/' . $code,
                ['message' => $this->exception->getMessage()]
            );

            return $response->withStatus($code);
        }

        if (class_exists(Run::class) && setting('displayErrorDetails')) {
            $handler = Misc::isAjaxRequest() ?
                new JsonResponseHandler() :
                new PrettyPageHandler();

            $whoops = new Run();
            $whoops->pushHandler($handler);

            $response->getBody()->write($whoops->handleException($this->exception));
        }

        return $response;
    }
}
