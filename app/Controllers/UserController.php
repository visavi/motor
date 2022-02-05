<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\User;
use App\Services\Validator;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * UserController
 */
class UserController extends Controller
{
    /**
     * Login
     *
     * @param Request   $request
     * @param Response  $response
     * @param Validator $validator
     *
     * @return Response
     */
    public function login(Request $request, Response $response, Validator $validator): Response
    {
        if ($request->getMethod() === 'POST') {
            $input = (array) $request->getParsedBody();

            $validator->required(['login', 'password']);

            if ($validator->isValid($input)) {
                $user = User::query()->where('login', $input['login'])->first();

                if ($user && password_verify($input['password'], $user->password)) {
                    $this->session->set('login', $user->login);
                    $this->session->set('password', $user->password);
                    $this->session->set('flash', ['success' => 'Вы успешно авторизованы!']);

                    return $response->withHeader('Location', '/guestbook');
                }

                $validator->addError('login', 'Неверный логин или пароль');
            }

            $this->session->set('flash', ['errors' => $validator->getErrors(), 'old' => $input]);

            return $response->withHeader('Location', '/login');
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
     * @param Validator $validator
     *
     * @return Response
     */
    public function register(Request $request, Response $response, Validator $validator): Response
    {
        if ($request->getMethod() === 'POST') {
            $input = (array) $request->getParsedBody();

            $validator->required(['login', 'password', 'password2', 'email', 'captcha'])
                ->add('captcha', fn () => $this->session->get('captcha') === $input['captcha'], 'Не удалось пройти проверку captcha!')
                ->length('login', 3, 20)
                ->regex('login', '|^[a-z0-9\-]+$|i')
                ->email('email')
                ->minLength(['password', 'password2'], 6)
                ->equal('password', 'password2');

            $userExists = User::query()->where('login', $input['login'])->first();
            if ($userExists) {
                $validator->addError('login', 'Данный логин уже занят');
            }

            $emailExists = User::query()->where('email', $input['email'])->first();
            if ($emailExists) {
                $validator->addError('email', 'Данный email уже используется');
            }

            if ($validator->isValid($input)) {
                $password = password_hash($input['password'], PASSWORD_BCRYPT);
                User::query()->insert([
                    'login'      => sanitize($input['login']),
                    'password'   => $password,
                    'email'      => sanitize($input['email']),
                    'role'       => User::USER,
                    'created_at' => time(),
                ]);

                $this->session->set('login', $input['login']);
                $this->session->set('password', $password);
                $this->session->set('flash', ['success' => 'Вы успешно зарегистрировались!']);

                return $response->withHeader('Location', '/guestbook');
            }

            $this->session->set('flash', ['errors' => $validator->getErrors(), 'old' => $input]);

            return $response->withHeader('Location', '/register');
        }

        return $this->view->render(
            $response,
            'users/register',
        );
    }

    /**
     * Logout
     *
     * @param Response $response
     *
     * @return Response
     */
    public function logout(Response $response): Response
    {
        $this->session->remove('login');
        $this->session->remove('password');

        $this->session->set('flash', ['success' => 'Вы успешно вышли!']);

        return $response->withHeader('Location', '/guestbook');
    }
}
