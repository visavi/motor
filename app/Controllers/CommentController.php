<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Comment;
use App\Repositories\CommentRepository;
use App\Repositories\StoryRepository;
use App\Services\Session;
use App\Services\Validator;
use App\Services\View;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * CommentController
 */
class CommentController extends Controller
{
    public function __construct(
        protected View $view,
        protected Session $session,
        protected Validator $validator,
        protected StoryRepository $storyRepository,
        protected CommentRepository $commentRepository,
    ) {}

    /**
     * Store
     *
     * @param int      $id
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function store(
        int $id,
        Request $request,
        Response $response,
    ): Response {
        if (! $user = getUser()) {
            abort(403, 'Доступ запрещен!');
        }

        $story = $this->storyRepository->getById($id);
        if (! $story) {
            abort(404, 'Статья не найдена!');
        }

        $input = (array) $request->getParsedBody();

        $this->validator
            ->required(['csrf', 'text'])
            ->same('csrf', $this->session->get('csrf'), 'Неверный идентификатор сессии, повторите действие!')
            ->length('text', setting('comment.text_min_length'), setting('comment.text_max_length'));

        if ($this->validator->isValid($input)) {
            Comment::query()->insert([
                'story_id'   => $story->id,
                'user_id'    => $user->id,
                'text'       => sanitize($input['text']),
                'rating'     => 0,
                'created_at' => time(),
            ]);

            $this->session->set('flash', ['success' => 'Комментарий успешно добавлен!']);
        } else {
            $this->session->set('flash', ['errors' => $this->validator->getErrors(), 'old' => $input]);
        }

        return $this->redirect($response, $story->getLink());
    }

    /**
     * Edit
     *
     * @param int      $id
     * @param int      $cid
     * @param Response $response
     *
     * @return Response
     */
    public function edit(int $id, int $cid, Response $response): Response
    {
        $story = $this->storyRepository->getById($id);
        if (! $story) {
            abort(404, 'Статья не найдена!');
        }

        $comment = $this->commentRepository->getById($cid);
        if (! $comment) {
            abort(404, 'Комментарий не найден!');
        }

        return $this->view->render(
            $response,
            'comments/edit',
            compact('story', 'comment')
        );
    }

    /**
     * Store
     *
     * @param int      $id
     * @param int      $cid
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function update(
        int $id,
        int $cid,
        Request $request,
        Response $response,
    ): Response
    {
        $story = $this->storyRepository->getById($id);
        if (! $story) {
            abort(404, 'Статья не найдена!');
        }

        $comment = $this->commentRepository->getById($cid);
        if (! $comment) {
            abort(404, 'Комментарий не найден!');
        }

        $input = (array) $request->getParsedBody();

        $this->validator
            ->required(['csrf', 'text'])
            ->same('csrf', $this->session->get('csrf'), 'Неверный идентификатор сессии, повторите действие!')
            ->length('text', setting('comment.text_min_length'), setting('comment.text_max_length'));

        if ($this->validator->isValid($input)) {
            $comment->update([
                'text'  => sanitize($input['text']),
            ]);

            $this->session->set('flash', ['success' => 'Комментарий успешно изменен!']);

            return $this->redirect($response, $story->getLink());
        }

        $this->session->set('flash', ['errors' => $this->validator->getErrors(), 'old' => $input]);

        return $this->redirect($response, '/' . $id . '/comments/' . $cid . '/edit');
    }

    /**
     * Destroy
     *
     * @param int      $id
     * @param int      $cid
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function destroy(int $id, int $cid, Request $request, Response $response): Response
    {
        $story = $this->storyRepository->getById($id);
        if (! $story) {
            abort(404, 'Статья не найдена!');
        }

        $comment = $this->commentRepository->getById($cid);
        if (! $comment) {
            abort(404, 'Комментарий не найден!');
        }

        $input = (array) $request->getParsedBody();

        $this->validator
            ->required('csrf')
            ->same('csrf', $this->session->get('csrf'), 'Неверный идентификатор сессии, повторите действие!');

        if ($this->validator->isValid($input)) {
            $comment->delete();

            $this->session->set('flash', ['success' => 'Комментарий успешно удален!']);
        } else {
            $this->session->set('flash', ['errors' => $this->validator->getErrors()]);
        }

        return $this->redirect($response, $story->getLink());
    }
}
