<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Services\Str;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

final class StartSessionMiddleware implements Middleware
{
    public function process(
        Request $request,
        RequestHandler $handler
    ): Response {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['csrf'])) {
            $_SESSION['csrf'] = Str::random();
        }

        //$request = $request->withAttribute('session', $_SESSION);

        return $handler->handle($request);
    }
}
