<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\File;
use App\Models\Story;
use App\Repositories\FileRepository;
use App\Repositories\StoryRepository;
use App\Services\Session;
use App\Services\Validator;
use App\Services\View;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * StoryController
 */
class StoryController extends Controller
{
    public function __construct(
        protected View $view,
        protected Session $session,
        protected Validator $validator,
        protected FileRepository $fileRepository,
        protected StoryRepository $storyRepository,
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
        $posts = $this->storyRepository->getPosts(setting('story.per_page'));

        return $this->view->render(
            $response,
            'stories/index',
            compact('posts')
        );
    }

    /**
     * View
     *
     * @param int      $id
     * @param Response $response
     *
     * @return Response
     */
    public function view(int $id, Response $response): Response
    {
        $post = $this->storyRepository->getById($id);
        if (! $post) {
            abort(404, 'Статья не найдена!');
        }

        $files = $this->fileRepository->getFilesByPostId($post->id);

        return $this->view->render(
            $response,
            'stories/view',
            compact('post', 'files')
        );
    }

    /**
     * Create
     *
     * @param Response $response
     *
     * @return Response
     */
    public function create(Response $response): Response
    {
        if (! $user = getUser()) {
            abort(403, 'Авторизуйтесь для добавления статей!');
        }

        $files = $this->fileRepository->getFiles($user->id, 0);

        return $this->view->render(
            $response,
            'stories/create',
            compact('files')
        );
    }

    /**
     * Store
     *
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function store(
        Request $request,
        Response $response,
    ): Response {
        if (! $user = getUser()) {
            abort(403, 'Доступ запрещен!');
        }

        $input = (array) $request->getParsedBody();

        $this->validator
            ->required(['csrf', 'title', 'text'])
            ->same('csrf', $this->session->get('csrf'), 'Неверный идентификатор сессии, повторите действие!')
            ->length('title', setting('story.title_min_length'), setting('story.title_max_length'))
            ->length('text', setting('story.text_min_length'), setting('story.text_max_length'));

        if ($this->validator->isValid($input)) {
            $postId = Story::query()->insert([
                'user_id'    => $user->id,
                'title'      => sanitize($input['title']),
                'text'       => sanitize($input['text']),
                'created_at' => time(),
            ]);

            File::query()
                ->where('post_id', 0)
                ->where('user_id', $user->id)
                ->update(['post_id' => $postId]);

            $this->session->set('flash', ['success' => 'Статья успешно добавлена!']);

            return $this->redirect($response, '/' . $postId);
        }

        $this->session->set('flash', ['errors' => $this->validator->getErrors(), 'old' => $input]);

        return $this->redirect($response, '/create');
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

        $post = $this->storyRepository->getById($id);
        if (! $post) {
            abort(404, 'Статья не найдена!');
        }

        $files = $this->fileRepository->getFilesByPostId($post->id);

        return $this->view->render(
            $response,
            'stories/edit',
            compact('post', 'files')
        );
    }

    /**
     * Store
     *
     * @param int      $id
     * @param Request  $request
     * @param Response $response
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

        $post = $this->storyRepository->getById($id);
        if (! $post) {
            abort(404, 'Статья не найдена!');
        }

        $input = (array) $request->getParsedBody();

        $this->validator
            ->required(['csrf', 'title', 'text'])
            ->same('csrf', $this->session->get('csrf'), 'Неверный идентификатор сессии, повторите действие!')
            ->length('title', setting('story.title_min_length'), setting('story.title_max_length'))
            ->length('text', setting('story.text_min_length'), setting('story.text_max_length'));

        if ($this->validator->isValid($input)) {
            $post->update([
                'title' => sanitize($input['title']),
                'text'  => sanitize($input['text']),
            ]);

            $this->session->set('flash', ['success' => 'Статья успешно изменена!']);

            return $this->redirect($response, '/' . $id);
        }

        $this->session->set('flash', ['errors' => $this->validator->getErrors(), 'old' => $input]);

        return $this->redirect($response, '/' . $id . '/edit');
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

        $post = $this->storyRepository->getById($id);
        if (! $post) {
            abort(404, 'Статья не найдена');
        }

        $input = (array) $request->getParsedBody();

        $this->validator
            ->required('csrf')
            ->same('csrf', $this->session->get('csrf'), 'Неверный идентификатор сессии, повторите действие!');

        if ($this->validator->isValid($input)) {
            $post->delete();

            $this->session->set('flash', ['success' => 'Статья успешно удалена!']);
        } else {
            $this->session->set('flash', ['errors' => $this->validator->getErrors()]);
        }

        return $this->redirect($response, '/');
    }
}
