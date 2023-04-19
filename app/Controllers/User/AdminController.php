<?php

declare(strict_types=1);

namespace App\Controllers\User;

use App\Controllers\Controller;
use App\Models\User;
use App\Repositories\FileRepository;
use App\Repositories\ReadRepository;
use App\Repositories\StoryRepository;
use App\Services\Session;
use App\Services\Validator;
use App\Services\View;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * AdminController
 */
class AdminController extends Controller
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
     * Edit
     *
     * @param string   $login
     * @param Response $response
     *
     * @return Response
     */
    public function edit(string $login, Response $response): Response
    {
        $user = User::query()->where('login', $login)->first();

        if (! $user) {
            abort(404, 'Пользователь не найден!');
        }

        $isManagement = true;

        return $this->view->render(
            $response,
            'profile/profile',
            compact('user', 'isManagement'),
        );
    }

    /**
     * Store
     *
     * @param string   $login
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function store(string $login, Request $request, Response $response): Response {

        $user = User::query()->where('login', $login)->first();

        if (! $user) {
            abort(404, 'Пользователь не найден!');
        }

        $input = (array) $request->getParsedBody();

        $this->validator
            ->required(['csrf', 'role', 'email'])
            ->same('csrf', $this->session->get('csrf'), 'Неверный идентификатор сессии, повторите действие!')
            ->in('role', User::ROLES)
            ->length('email', 5, 100)
            ->email('email')
            ->length('name', 3, 20);

        if ($this->validator->isValid($input)) {

            $user->update([
                'role'  => sanitize($input['role']),
                'email' => sanitize($input['email']),
                'name'  => sanitize($input['name']),
            ]);

            $this->session->set('flash', ['success' => 'Данные успешно изменены!']);
        } else {
            $this->session->set('flash', ['errors' => $this->validator->getErrors(), 'old' => $input]);
        }

        return $this->redirect($response, route('user-edit', ['login' => $user->login]));
    }

    /**
     * Destroy
     *
     * @param string   $login
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function destroy(string $login, Request $request, Response $response): Response
    {
        $user = User::query()->where('login', $login)->first();

        if (! $user) {
            abort(404, 'Пользователь не найден!');
        }

        if (! isAdmin(User::ADMIN)) {
            abort(403, 'У вас нет прав на удаление пользователей!');
        }

        $input = (array) $request->getParsedBody();

        $this->validator
            ->required('csrf')
            ->same('csrf', $this->session->get('csrf'), 'Неверный идентификатор сессии, повторите действие!')
            ->custom(! in_array($user->role, User::ADMIN_ROLES, true), 'Нельзя удалять администраторов!');

        if ($this->validator->isValid($input)) {
            $user->delete();
        } else {
            $this->session->set('flash', ['errors' => $this->validator->getErrors()]);

            return $this->redirect($response, route('user-edit', ['login' => $user->login]));
        }

        $this->session->set('flash', ['success' => 'Пользователь успешно удален!']);

        return $this->redirect($response, route('users'));
    }
}
