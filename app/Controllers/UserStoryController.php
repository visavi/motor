<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\User;
use App\Repositories\FileRepository;
use App\Repositories\ReadRepository;
use App\Repositories\StoryRepository;
use App\Services\Session;
use App\Services\Validator;
use App\Services\View;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * UserStoryController
 */
class UserStoryController extends Controller
{
    public function __construct(
        protected View $view,
        protected Session $session,
        protected Validator $validator,
        protected FileRepository $fileRepository,
        protected StoryRepository $storyRepository,
        protected ReadRepository $readRepository,
    ) {}

    /**
     * Index
     *
     * @param string   $login
     * @param Response $response
     *
     * @return Response
     */
    public function index(string $login, Response $response): Response
    {
        $user = User::query()->where('login', $login)->first();

        if (! $user) {
            abort(404, 'Пользователь не найден!');
        }

        $stories = $this->storyRepository->getStoriesByUserId($user->id, setting('story.per_page'));

        return $this->view->render(
            $response,
            'users/story',
            compact('stories', 'user')
        );
    }
}
