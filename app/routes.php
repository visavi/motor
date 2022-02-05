<?php
declare(strict_types=1);

use App\Controllers\CaptchaController;
use App\Controllers\GuestbookController;
use App\Controllers\HomeController;
use App\Controllers\UserController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    /*$app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });*/

    $app->get('/', [HomeController::class, 'home']);

    $app->get('/captcha', [CaptchaController::class, 'captcha']);

    $app->map(['GET', 'POST'], '/login', [UserController::class, 'login']);
    $app->map(['GET', 'POST'], '/register', [UserController::class, 'register']);
    $app->get('/logout', [UserController::class, 'logout']);

    $app->group('/guestbook', function (Group $group) {
        $group->get('', [GuestbookController::class, 'index']);
        $group->post('/create', [GuestbookController::class, 'create']);
        $group->get('/{id:[0-9]+}/edit', [GuestbookController::class, 'edit']);
        $group->post('/{id:[0-9]+}/edit', [GuestbookController::class, 'store']);
        $group->get('/{id:[0-9]+}/delete', [GuestbookController::class, 'delete']);
    });
};
