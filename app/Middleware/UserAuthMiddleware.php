<?php

namespace App\Middleware;

use App\Models\User;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class UserAuthMiddleware implements MiddlewareInterface
{
    /*public function __construct(
        private SessionInterface $session
    ) {}*/

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        if (isset($_COOKIE['login'], $_COOKIE['password']) && ! isset($_SESSION['login'])) {
            $user = User::query()->where('login', $_COOKIE['login'])->first();

            if ($user && $_COOKIE['login'] === $user->login && $_COOKIE['password'] === $user->password) {
                $_SESSION['login'] = $user->login;
                $_SESSION['password'] = $user->password;
            }
        }

        return $handler->handle($request);
    }
}
