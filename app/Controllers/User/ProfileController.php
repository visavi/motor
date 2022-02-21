<?php

declare(strict_types=1);

namespace App\Controllers\User;

use App\Controllers\Controller;
use App\Services\Session;
use App\Services\Validator;
use App\Services\View;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * ProfileController
 */
class ProfileController extends Controller
{
    public function __construct(
        protected View $view,
        protected Session $session,
    ) {}

    /**
     * Profile
     *
     * @param Response $response
     *
     * @return Response
     */
    public function index(Response $response): Response
    {
        if (! $user = getUser()) {
            abort(403, 'Для выполнения действия необходимо авторизоваться!');
        }

        return $this->view->render(
            $response,
            'profile/profile',
            compact('user'),
        );
    }

    /**
     * Store
     *
     * @param Request   $request
     * @param Response  $response
     * @param Validator $validator
     *
     * @return Response
     */
    public function store(Request $request, Response $response, Validator $validator): Response
    {
        if (! $user = getUser()) {
            abort(403, 'Для выполнения действия необходимо авторизоваться!');
        }

        $input = (array) $request->getParsedBody();

        $validator
            ->required(['email'])
            ->length('email', 5, 100)
            ->email('email')
            ->length('name', 3, 20);

        if ($validator->isValid($input)) {
            $user->update([
                'email' => sanitize($input['email']),
                'name'  => sanitize($input['name']),
            ]);

            $this->session->set('flash', ['success' => 'Данные успешно изменены!']);
        } else {
            $this->session->set('flash', ['errors' => $validator->getErrors(), 'old' => $input]);
        }

        return $this->redirect($response, '/profile');
    }
}
