<?php

namespace App\Middleware;

use App\Models\User;
use App\Services\Session;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

final class UserAuthMiddleware implements Middleware
{
    public function __construct(
        private Session $session
    ) {}

    public function process(
        Request $request,
        RequestHandler $handler
    ): Response {
        if (isset($_COOKIE['login'], $_COOKIE['password']) && ! $this->session->has('login')) {
            $user = User::query()->where('login', $_COOKIE['login'])->first();

            if ($user && $_COOKIE['login'] === $user->login && $_COOKIE['password'] === $user->password) {
                $this->session->set('login', $user->login);
                $this->session->set('password', $user->password);
            }
        }

        return $handler->handle($request);
    }
}
