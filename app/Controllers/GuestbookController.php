<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Guestbook;
use App\Repositories\GuestbookRepository;
use App\Services\NotificationService;
use App\Services\Session;
use App\Services\Validator;
use App\Services\View;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * GuestbookController
 */
class GuestbookController extends Controller
{
    public function __construct(
        protected View $view,
        protected Session $session,
        protected Validator $validator,
        protected GuestbookRepository $guestbookRepository,
    ) {}

    /**
     * Index
     *
     * @param Response $response
     *
     * @return Response
     */
    public function index(Response $response): Response
    {
        $messages = $this->guestbookRepository->getMessages(setting('guestbook.per_page'));

        return $this->view->render(
            $response,
            'guestbook/index',
            compact('messages')
        );
    }

    /**
     * Store
     *
     * @param Request $request
     * @param Response $response
     * @param NotificationService $notificationService
     *
     * @return Response
     */
    public function store(
        Request $request,
        Response $response,
        NotificationService $notificationService,
    ): Response {
        $user = getUser();

        if (! $user && ! setting('guestbook.allow_guests')) {
            abort(403, 'Доступ запрещен!');
        }

        $input = (array) $request->getParsedBody();

        $this->validator
            ->required(['csrf', 'text'])
            ->same('csrf', $this->session->get('csrf'), 'Неверный идентификатор сессии, повторите действие!')
            ->length('text', setting('guestbook.text_min_length'), setting('guestbook.text_max_length'))
            ->boolean('active');

        if (! $user) {
            $this->validator
                ->required('captcha')
                ->length('name', setting('guestbook.name_min_length'), setting('guestbook.name_max_length'))
                ->same('captcha', $this->session->get('captcha'), 'Не удалось пройти проверку captcha!');
        }

        if ($this->validator->isValid($input)) {
            if (! $user) {
                $name = isset($input['name']) ? sanitize($input['name']) : setting('main.guest_name');
            }

            $text   = sanitize($input['text']);
            $active = isAdmin() ? true : setting('guestbook.active');

            Guestbook::query()->create([
                'user_id'    => $user->id ?? null,
                'text'       => $text,
                'name'       => $name ?? null,
                'active'     => $active,
                'created_at' => time(),
            ]);

            $notificationService->sendNotify($text, route('guestbook'), 'Гостевая');

            $this->session->set('flash', [
                'success' => $active
                    ? 'Сообщение успешно добавлено!'
                    : 'Сообщение будет опубликовано после модерации!'
            ]);
        } else {
            $this->session->set('flash', ['errors' => $this->validator->getErrors(), 'old' => $input]);
        }

        return $this->redirect($response, '/guestbook');
    }

    /**
     * Edit
     *
     * @param int      $id
     * @param Response $response
     *
     * @return Response
     */
    public function edit(int $id, Response $response): Response
    {
        if (! isAdmin()) {
            abort(403, 'Доступ запрещен!');
        }

        $message = $this->guestbookRepository->getById($id);
        if (! $message) {
            abort(404, 'Сообщение не найдено');
        }

        return $this->view->render(
            $response,
            'guestbook/edit',
            compact('message')
        );
    }

    /**
     * Store
     *
     * @param int          $id
     * @param Request      $request
     * @param Response     $response
     *
     * @return Response
     */
    public function update(
        int $id,
        Request $request,
        Response $response,
    ): Response
    {
        if (! isAdmin()) {
            abort(403, 'Доступ запрещен!');
        }

        $message = $this->guestbookRepository->getById($id);
        if (! $message) {
            abort(404, 'Сообщение не найдено');
        }

        $input = (array) $request->getParsedBody();

        $this->validator
            ->required(['csrf', 'text'])
            ->same('csrf', $this->session->get('csrf'), 'Неверный идентификатор сессии, повторите действие!')
            ->length('text', setting('guestbook.text_min_length'), setting('guestbook.text_max_length'))
            ->boolean('active');

        if ($this->validator->isValid($input)) {
            $message->update([
                'text'   => sanitize($input['text']),
                'active' => $input['active'],
            ]);
        } else {
            $this->session->set('flash', ['errors' => $this->validator->getErrors(), 'old' => $input]);

            return $this->redirect($response, '/guestbook/' . $id . '/edit');
        }

        $this->session->set('flash', ['success' => 'Сообщение успешно изменено!']);

        return $this->redirect($response, '/guestbook');
    }

    /**
     * Destroy
     *
     * @param int       $id
     * @param Request   $request
     * @param Response  $response
     *
     * @return Response
     */
    public function destroy(int $id, Request $request, Response $response): Response
    {
        if (! isAdmin()) {
            abort(403, 'Доступ запрещен!');
        }

        $message = $this->guestbookRepository->getById($id);
        if (! $message) {
            abort(404, 'Сообщение не найдено');
        }

        $input = (array) $request->getParsedBody();

        $this->validator
            ->required('csrf')
            ->same('csrf', $this->session->get('csrf'), 'Неверный идентификатор сессии, повторите действие!');

        if ($this->validator->isValid($input)) {
            $message->delete();

            $this->session->set('flash', ['success' => 'Сообщение успешно удалено!']);
        } else {
            $this->session->set('flash', ['errors' => $this->validator->getErrors()]);
        }

        return $this->redirect($response, '/guestbook');
    }
}
