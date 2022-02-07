<?php

namespace App\Middleware;

use App\Models\User;
use Odan\Session\SessionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class UserAuthMiddleware implements MiddlewareInterface
{
    public function __construct(
        private SessionInterface $session
    ) {}

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        if (isset($_COOKIE['login'], $_COOKIE['password']) && ! $this->session->has('login')) {
            $user = User::query()->where('login', $_COOKIE['login'])->first();

            if ($user && $_COOKIE['login'] === $user->login && $_COOKIE['password'] === $user->password) {
                $this->session->set('login', $user->login);
                $this->session->set('password', $user->password);
                $this->session->save();
            }
        }

        return $handler->handle($request);
    }
}
