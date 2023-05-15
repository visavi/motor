<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

final class CheckAccessMiddleware implements Middleware
{
    public function process(
        Request $request,
        RequestHandler $handler,
    ): Response {
        $user     = getUser();
        $response = $handler->handle($request);

        if ($user && ! in_array(currentRoute(), ['/', '/logout'], true)) {
            if ($user->isBanned()) {
                abort(403, 'Вы забанены!');
            }

            if ($user->isPended() && ! str_starts_with(currentRoute(), '/confirm')) {
                abort(403, 'Необходимо подтвердить email по ссылки в письме!');
            }
        }

        return $response;
    }
}
