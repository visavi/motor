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
    /**
     * Реже одного раза в минуту отметку не обновляем: каждая запись
     * переписывает таблицу целиком
     */
    private const THROTTLE = 60;

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

        $this->markOnline();

        return $handler->handle($request);
    }

    /**
     * Отмечает время последнего визита пользователя
     *
     * @return void
     */
    private function markOnline(): void
    {
        $user = getUser();

        if (! $user || ! $user->id) {
            return;
        }

        if ($user->online_at > time() - self::THROTTLE) {
            return;
        }

        $user->update(['online_at' => time()]);
    }
}
