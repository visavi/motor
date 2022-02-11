<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Guestbook;
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
/*        $total = Guestbook::query()->count();
        //$paginator->setView(basePath('/resources/views/app/_paginator.php'));
        $paginator = $paginator->create($total);

        $messages = Guestbook::query()
            ->offset($paginator->offset)
            ->limit($paginator->limit)
            ->orderByDesc('created_at')
            ->get();

        var_dump($paginator->links()); exit;*/


        $messages = $this->guestbookRepository->getMessages(settings('guestbook')['per_page']);

        return $this->view->render(
            $response,
            'guestbook/index',
            compact('messages')
        );
    }

    /**
     * Create
     *
     * @param Request   $request
     * @param Response  $response
     * @param Validator $validator
     *
     * @return Response
     */
    public function create(Request $request, Response $response, Validator $validator): Response
    {
        if (! isUser()) {
            abort(403, 'Доступ запрещен!');
        }

        $input = (array) $request->getParsedBody();
        $files = $request->getUploadedFiles();
        $input = array_merge($input, $files);

        $validator
            ->required(['title', 'text'])
            ->add('user', fn () => isUser(), 'Необходимо авторизоваться!')
            ->length('title', settings('guestbook')['title_min_length'], settings('guestbook')['title_max_length'])
            ->length('text', settings('guestbook')['text_min_length'], settings('guestbook')['text_max_length'])
            ->file('image', [
                'size_max'   => 5000000,
                'weight_min' => 100,
            ]);

        if ($validator->isValid($input)) {
            if ($input['image']->getError() === UPLOAD_ERR_OK) {
                $extension = getExtension($input['image']->getClientFilename());
                $path = '/uploads/guestbook/' . uniqueName($extension);
                $input['image']->moveTo(publicPath($path));
            }

            Guestbook::query()->insert([
                'login'      => $this->session->get('login'),
                'title'      => sanitize($input['title']),
                'text'       => sanitize($input['text']),
                'image'      => $path ?? null,
                'created_at' => time(),
            ]);
        } else {
            $this->session->set('flash', ['errors' => $validator->getErrors(), 'old' => $input]);
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

        $message = Guestbook::query()->find($id);
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
     * @param int       $id
     * @param Request   $request
     * @param Response  $response
     * @param Validator $validator
     *
     * @return Response
     */
    public function store(int $id, Request $request, Response $response, Validator $validator): Response
    {
        if (! isAdmin()) {
            abort(403, 'Доступ запрещен!');
        }

        $message = Guestbook::query()->find($id);
        if (! $message) {
            abort(404, 'Сообщение не найдено');
        }

        $input = (array) $request->getParsedBody();

        $validator
            ->required(['title', 'text'])
            ->length('title', 5, 50)
            ->length('text', 5, 5000);

        if ($validator->isValid($input)) {
            $message->update([
                'title' => sanitize($input['title']),
                'text'  => sanitize($input['text']),
            ]);
        } else {
            $this->session->set('flash', ['errors' => $validator->getErrors(), 'old' => $input]);

            return $response->withHeader('Location', '/guestbook/' . $id . '/edit');
        }

        $this->session->set('flash', ['success' => 'Сообщение успешно изменено!']);

        return $this->redirect($response, '/guestbook');
    }

    /**
     * Delete
     *
     * @param int      $id
     * @param Response $response
     *
     * @return Response
     */
    public function delete(int $id, Response $response): Response
    {
        if (! isAdmin()) {
            abort(403, 'Доступ запрещен!');
        }

        $message = Guestbook::query()->find($id);

        if ($message) {
            $message->delete();

            if ($message->image && file_exists(publicPath($message->image))) {
                unlink(publicPath($message->image));
            }
        }

        $this->session->set('flash', ['success' => 'Сообщение успешно удалено!']);

        return $this->redirect($response, '/guestbook');
    }
}
