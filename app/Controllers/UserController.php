<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\User;
use App\Services\Session;
use App\Services\Validator;
use App\Services\View;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * UserController
 */
class UserController extends Controller
{
    public function __construct(
        protected View $view,
        protected Session $session,
        protected Validator $validator,
    ) {}

    /**
     * Login
     *
     * @param Request   $request
     * @param Response  $response
     *
     * @return Response
     */
    public function login(Request $request, Response $response): Response
    {
        if ($request->getMethod() === 'POST') {
            $input = (array) $request->getParsedBody();

            $this->validator->required(['login', 'password']);

            if ($this->validator->isValid($input)) {
                $user = User::query()->where('login', $input['login'])->first();

                if ($user && password_verify($input['password'], $user->password)) {
                    $this->session->set('login', $user->login);
                    $this->session->set('password', $user->password);
                    $this->session->set('flash', ['success' => 'Вы успешно авторизованы!']);

                    // @TODO remember
                    $options = [
                        'expires'  => strtotime('+1 year'),
                        'path'     => '/',
                        'domain'   => setting('session.cookie_domain'),
                        'secure'   => setting('session.cookie_secure'),
                        'httponly' => setting('session.cookie_httponly'),
                        'samesite' => setting('session.cookie_samesite'),
                    ];
                    setcookie('login', $user->login, $options);
                    setcookie('password', $user->password, $options);

                    return $this->redirect($response, '/');
                }

                $this->validator->addError('login', 'Неверный логин или пароль');
            }

            $this->session->set('flash', ['errors' => $this->validator->getErrors(), 'old' => $input]);

            return $this->redirect($response, '/login');
        }

        return $this->view->render(
            $response,
            'users/login',
        );
    }

    /**
     * Register
     *
     * @param Request   $request
     * @param Response  $response
     *
     * @return Response
     */
    public function register(Request $request, Response $response): Response
    {
        if ($request->getMethod() === 'POST') {
            $input = (array) $request->getParsedBody();

            $this->validator->required(['captcha', 'login', 'password', 'password2', 'email'])
                ->same('captcha', $this->session->get('captcha'), 'Не удалось пройти проверку captcha!')
                ->length('login', 3, 20)
                ->regex('login', '|^[a-z0-9\-]+$|i')
                ->email('email')
                ->minLength(['password', 'password2'], 6)
                ->equal('password', 'password2');

            $userExists = User::query()->where('login', 'lax', $input['login'])->first();
            if ($userExists) {
                $this->validator->addError('login', 'Данный логин уже занят');
            }

            $emailExists = User::query()->where('email', 'lax', $input['email'])->first();
            if ($emailExists) {
                $this->validator->addError('email', 'Данный email уже используется');
            }

            if ($this->validator->isValid($input)) {
                $password = password_hash($input['password'], PASSWORD_BCRYPT);
                User::query()->create([
                    'login'      => sanitize($input['login']),
                    'password'   => $password,
                    'email'      => strtolower($input['email']),
                    'role'       => User::USER,
                    'created_at' => time(),
                ]);

                $this->session->set('login', $input['login']);
                $this->session->set('password', $password);
                $this->session->set('flash', ['success' => 'Вы успешно зарегистрировались!']);

                return $this->redirect($response, '/');
            }

            $this->session->set('flash', ['errors' => $this->validator->getErrors(), 'old' => $input]);

            return $this->redirect($response, '/register');
        }

        return $this->view->render(
            $response,
            'users/register',
        );
    }

    /**
     * Logout
     *
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function logout(Request $request, Response $response): Response
    {
        $input = (array) $request->getParsedBody();

        $this->validator
            ->required('csrf')
            ->same('csrf', $this->session->get('csrf'), 'Неверный идентификатор сессии, повторите действие!');

        if ($this->validator->isValid($input)) {
            $this->session->delete('login');
            $this->session->delete('password');

            $options = [
                'expires' => strtotime('-1 hour'),
                'path' => '/',
                'domain'   => setting('session.cookie_domain'),
                'secure'   => setting('session.cookie_secure'),
                'httponly' => setting('session.cookie_httponly'),
                'samesite' => setting('session.cookie_samesite'),
            ];
            setcookie('password', '', $options);

            $this->session->set('flash', ['success' => 'Вы успешно вышли!']);
        } else {
            $this->session->set('flash', ['errors' => $this->validator->getErrors()]);
        }

        return $this->redirect($response, '/');
    }

    /**
     * User
     *
     * @param string   $login
     * @param Response $response
     *
     * @return Response
     */
    public function user(string $login, Response $response): Response
    {
        $user = User::query()->where('login', $login)->first();

        if (! $user) {
            abort(404, 'Пользователь не найден!');
        }

        return $this->view->render(
            $response,
            'users/user',
            compact('user')
        );
    }
}
