<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\File;
use App\Models\Guestbook;
use App\Repositories\FileRepository;
use App\Repositories\GuestbookRepository;
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
        protected FileRepository $fileRepository,
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
        $files    = $this->fileRepository->getFiles(getUser('id'), 0);

        return $this->view->render(
            $response,
            'guestbook/index',
            compact('messages', 'files')
        );
    }

    /**
     * Create
     *
     * @param Request      $request
     * @param Response     $response
     *
     * @return Response
     */
    public function create(
        Request $request,
        Response $response,
    ): Response {
        $user = getUser();

        if (! $user && ! setting('guestbook.allow_guests')) {
            abort(403, 'Доступ запрещен!');
        }

        $input = (array) $request->getParsedBody();

        $this->validator
            ->required(['csrf', 'title', 'text'])
            ->same('csrf', $this->session->get('csrf'), 'Неверный идентификатор сессии, повторите действие!')
            ->length('title', setting('guestbook.title_min_length'), setting('guestbook.title_max_length'))
            ->length('text', setting('guestbook.text_min_length'), setting('guestbook.text_max_length'));

        if (! $user) {
            $this->validator
                ->required('captcha')
                ->same('captcha', $this->session->get('captcha'), 'Не удалось пройти проверку captcha!');
        }

        if ($this->validator->isValid($input)) {
            $messageId = Guestbook::query()->insert([
                'user_id'    => $user->id ?? null,
                'title'      => sanitize($input['title']),
                'text'       => sanitize($input['text']),
                'created_at' => time(),
            ]);

            if ($user) {
                File::query()
                    ->where('post_id', 0)
                    ->where('user_id', $user->id)
                    ->update(['post_id' => $messageId]);
            }

            $this->session->set('flash', ['success' => 'Сообщение успешно добавлено!']);
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

        $files = $this->fileRepository->getFilesByPostId($message->id);

        return $this->view->render(
            $response,
            'guestbook/edit',
            compact('message', 'files')
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
    public function store(
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
            ->required(['csrf', 'title', 'text'])
            ->same('csrf', $this->session->get('csrf'), 'Неверный идентификатор сессии, повторите действие!')
            ->length('title', setting('guestbook.title_min_length'), setting('guestbook.title_max_length'))
            ->length('text', setting('guestbook.text_min_length'), setting('guestbook.text_max_length'));

        if ($this->validator->isValid($input)) {
            $message->update([
                'title' => sanitize($input['title']),
                'text'  => sanitize($input['text']),
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
