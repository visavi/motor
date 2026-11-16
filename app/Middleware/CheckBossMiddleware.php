<?php

namespace App\Middleware;

use App\Models\User;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

final class CheckBossMiddleware implements Middleware
{
    public function process(
        Request $request,
        RequestHandler $handler
    ): Response {
        if (! isAdmin(User::BOSS)) {
            abort(403, 'Доступ запрещен!');
        }

        return $handler->handle($request);
    }
}
