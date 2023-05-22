<?php

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

final class TrailingSlashMiddleware implements Middleware
{
    public function __construct(
        private Response $response
    ) {}

    public function process(
        Request $request,
        RequestHandler $handler
    ): Response {
        $uri = $request->getUri();
        $path = $uri->getPath();

        if ($path !== '/' && str_ends_with($path, '/')) {
            $path = rtrim($path, '/');

            // permanently redirect paths with a trailing slash
            // to their non-trailing counterpart
            $uri = $uri->withPath($path);

            if ($request->getMethod() === 'GET') {
                return $this->response
                    ->withHeader('Location', (string) $uri)
                    ->withStatus(301);
            }

            $request = $request->withUri($uri);
        }

        return $handler->handle($request);
    }
}
