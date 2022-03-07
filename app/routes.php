<?php

declare(strict_types=1);

use App\Controllers\BBCodeController;
use App\Controllers\StoryController;
use App\Controllers\CaptchaController;
use App\Controllers\GuestbookController;
use App\Controllers\UploadController;
use App\Controllers\User\ProfileController;
use App\Controllers\StickerController;
use App\Controllers\UserController;
use App\Middleware\CheckUserMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    /*$app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });*/

    //$app->get('/', [HomeController::class, 'home']);

    $app->group('', function (Group $group) {
        $group->get('/', [StoryController::class, 'index']);
        $group->get('/create', [StoryController::class, 'create']);
        $group->post('/', [StoryController::class, 'store']);
        $group->get('/{id:[0-9]+}', [StoryController::class, 'view']);
        $group->get('/{id:[0-9]+}/edit', [StoryController::class, 'edit']);
        $group->put('/{id:[0-9]+}', [StoryController::class, 'update']);
        $group->delete('/{id:[0-9]+}', [StoryController::class, 'destroy']);
    });

    $app->get('/captcha', [CaptchaController::class, 'captcha']);
    $app->get('/stickers/modal', [StickerController::class, 'modal']);
    $app->post('/bbcode', [BBCodeController::class, 'bbcode']);

    $app->group('/upload', function (Group $group) {
        $group->post('', [UploadController::class, 'upload']);
        $group->delete('/{id:[0-9]+}', [UploadController::class, 'destroy']);
    })->add(new CheckUserMiddleware());

    $app->map(['GET', 'POST'], '/login', [UserController::class, 'login']);
    $app->map(['GET', 'POST'], '/register', [UserController::class, 'register']);
    $app->post('/logout', [UserController::class, 'logout']);

    $app->group('/guestbook', function (Group $group) {
        $group->get('', [GuestbookController::class, 'index']);
        $group->post('', [GuestbookController::class, 'store']);
        $group->get('/{id:[0-9]+}/edit', [GuestbookController::class, 'edit']);
        $group->put('/{id:[0-9]+}', [GuestbookController::class, 'update']);
        $group->delete('/{id:[0-9]+}', [GuestbookController::class, 'destroy']);
    });

    $app->group('/users', function (Group $group) {
        $group->get('/{login:[\w\-]+}', [UserController::class, 'user']);
    });

    $app->get('/profile', [ProfileController::class, 'index']);
    $app->put('/profile', [ProfileController::class, 'store']);
};
