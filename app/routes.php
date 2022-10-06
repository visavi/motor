<?php

declare(strict_types=1);

use App\Controllers\BBCodeController;
use App\Controllers\CommentController;
use App\Controllers\FavoriteController;
use App\Controllers\RatingController;
use App\Controllers\SearchController;
use App\Controllers\StoryController;
use App\Controllers\CaptchaController;
use App\Controllers\GuestbookController;
use App\Controllers\TagController;
use App\Controllers\UploadController;
use App\Controllers\User\ProfileController;
use App\Controllers\StickerController;
use App\Controllers\UserController;
use App\Controllers\UserStoryController;
use App\Middleware\CheckAdminMiddleware;
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
        $group->get('/stories', [StoryController::class, 'index']);
        $group->get('/{slug:[\w\-]+\-[\d]+}', [StoryController::class, 'view']);
        $group->get('/stories/{login:[\w\-]+}', [UserStoryController::class, 'index']);

        $group->get('/tags', [TagController::class, 'index']);
        $group->get('/tags/{tag:.+}', [TagController::class, 'search']);

        // For user
        $group->group('', function (Group $group) {
            $group->post('/', [StoryController::class, 'store']);
            $group->get('/create', [StoryController::class, 'create']);
            $group->get('/{id:[0-9]+}/edit', [StoryController::class, 'edit']);
            $group->put('/{id:[0-9]+}', [StoryController::class, 'update']);
            $group->delete('/{id:[0-9]+}', [StoryController::class, 'destroy']);
            $group->post('/{id:[0-9]+}/comments', [CommentController::class, 'store']);
        })->add(CheckUserMiddleware::class);

        // Edit and delete comment (for admin)
        $group->group('/{id:[0-9]+}/comments/{cid:[0-9]+}', function (Group $group) {
            $group->get('/edit', [CommentController::class, 'edit']);
            $group->put('', [CommentController::class, 'update']);
            $group->delete('', [CommentController::class, 'destroy']);
        })->add(CheckAdminMiddleware::class);
    });

    // For user group
    $app->group('', function (Group $group) {
        // Upload
        $group->group('/upload', function (Group $group) {
            $group->post('', [UploadController::class, 'upload']);
            $group->delete('/{id:[0-9]+}', [UploadController::class, 'destroy']);
        });

        // Profile
        $group->group('/profile', function (Group $group) {
            $group->get('', [ProfileController::class, 'index']);
            $group->put('', [ProfileController::class, 'store']);
            $group->delete('/photo', [ProfileController::class, 'deletePhoto']);
        });

        // Change rating
        $group->post('/rating/{id:[0-9]+}', [RatingController::class, 'change']);

        // Favorites
        $group->group('/favorites', function (Group $group) {
            $group->get('', [FavoriteController::class, 'index']);
            // Add/delete to favorite
            $group->post('/{id:[0-9]+}', [FavoriteController::class, 'change']);
        });
    })->add(CheckUserMiddleware::class);

    $app->get('/captcha', [CaptchaController::class, 'captcha']);
    $app->get('/stickers/modal', [StickerController::class, 'modal']);
    $app->post('/bbcode', [BBCodeController::class, 'bbcode']);

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
        $group->get('', [UserController::class, 'index']);
        $group->get('/{login:[\w\-]+}', [UserController::class, 'user']);
    });

    $app->group('/search', function (Group $group) {
        $group->get('', [SearchController::class, 'index']);
    });
};
