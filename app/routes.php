<?php

declare(strict_types=1);

use App\Controllers\BBCodeController;
use App\Controllers\RatingController;
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
        $group->post('/', [StoryController::class, 'store']);
        $group->get('/tags', [StoryController::class, 'tags']);
        $group->get('/tags/{tag:.+}', [StoryController::class, 'searchTags']);
        $group->get('/create', [StoryController::class, 'create']);
        $group->get('/{slug:[\w\-]+\-[\d]+}', [StoryController::class, 'view']);
        $group->get('/{id:[0-9]+}/edit', [StoryController::class, 'edit']);
        $group->put('/{id:[0-9]+}', [StoryController::class, 'update']);
        $group->delete('/{id:[0-9]+}', [StoryController::class, 'destroy']);
    });

    $app->post('/rating/{id:[0-9]+}', [RatingController::class, 'change']);

    $app->get('/captcha', [CaptchaController::class, 'captcha']);
    $app->get('/stickers/modal', [StickerController::class, 'modal']);
    $app->post('/bbcode', [BBCodeController::class, 'bbcode']);

    $app->group('/upload', function (Group $group) {
        $group->post('', [UploadController::class, 'upload']);
        $group->delete('/{id:[0-9]+}', [UploadController::class, 'destroy']);
    })->add(CheckUserMiddleware::class);

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

    $app->group('/profile', function (Group $group) {
        $group->get('', [ProfileController::class, 'index']);
        $group->put('', [ProfileController::class, 'store']);
        $group->delete('/photo', [ProfileController::class, 'deletePhoto']);
    })->add(CheckUserMiddleware::class);
};
