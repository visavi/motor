<?php

declare(strict_types=1);

namespace App\Controllers\User;

use App\Controllers\Controller;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\Mail;
use App\Services\Session;
use App\Services\Str;
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
        protected UserRepository $userRepository,
    ) {}

    /**
     * Login
     *
     * @param Request   $request
     * @param Response  $response
     *
     * @return Response
     */
    public function index(Request $request, Response $response): Response
    {
        $users = $this->userRepository->getUsers(setting('user.per_page'));

        return $this->view->render(
            $response,
            'users/index',
            compact('users')
        );
    }

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
        if (isUser()) {
            return $this->redirect($response, '/');
        }

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
        if (! setting('main.allow_register')) {
            abort(200, 'Регистрация временно приостановлена, пожалуйста зайдите позже!');
        }

        if (isUser()) {
            return $this->redirect($response, '/');
        }

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
                $confirmEmail = setting('main.confirm_email');
                $password     = password_hash($input['password'], PASSWORD_BCRYPT);
                $confirmCode  = $confirmEmail ? Str::random() : '';
                $role         = $confirmEmail ? User::PENDED : User::USER;
                $login        = sanitize($input['login']);
                $email        = strtolower($input['email']);

                User::query()->create([
                    'login'        => $login,
                    'password'     => $password,
                    'email'        => $email,
                    'role'         => $role,
                    'created_at'   => time(),
                    'confirmed'    => false,
                    'confirm_code' => $confirmCode,
                ]);

                $confirmText = $confirmEmail ? sprintf(
                    '%sВаш проверочный код: %s',
                    PHP_EOL,
                    setting('app.url') . route('confirm', ['code' => $confirmCode])
                ) : '';

                $data = [
                    'to_email'   => $email,
                    'to_name'    => $login,
                    'subject'    => 'Регистрация на ' . setting('app.name'),
                    'text'       => sprintf(
                        'Добро пожаловать! Вы успешно зарегистрировались на сайте %s%s',
                        setting('app.url'),
                        $confirmText
                    ),
                    'from_email' => setting('mailer.from_email'),
                    'from_name'  => setting('mailer.from_name'),
                ];

                Mail::send($data);

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

    /**
     * Confirm email
     *
     * @param string $code
     * @param Response $response
     *
     * @return Response
     */
    public function confirm(string $code, Response $response): Response
    {
        $user = getUser();
        if (! $user) {
            $user = User::query()->where('confirm_code', $code)->first();
        }

        if ($user && $user->role !== User::PENDED) {
            return $this->redirect($response, '/');
        }

        if ($user && $user->confirm_code === $code) {
            $user->update([
                'role'         => User::USER,
                'confirm_code' => '',
            ]);

            $this->session->set('flash', ['success' => 'Email успешно подтвержден!']);
        } else {
            $this->session->set('flash', ['danger' => 'Проверочный код неверный!']);
        }

        return $this->redirect($response, '/');
    }
}
