<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\File;
use App\Models\Story;
use App\Repositories\FileRepository;
use App\Repositories\StoryRepository;
use App\Services\Session;
use App\Services\Slug;
use App\Services\Str;
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
     * @param string   $slug
     * @param Response $response
     *
     * @return Response
     */
    public function view(string $slug, Response $response): Response
    {
        $post = $this->storyRepository->getBySlug($slug);
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
     * By tags
     *
     * @param string   $tag
     * @param Response $response
     *
     * @return Response
     */
    public function searchTags(string $tag, Response $response): Response
    {
        $posts = $this->storyRepository->getPostsByTag(urldecode($tag), setting('story.per_page'));

        return $this->view->render(
            $response,
            'stories/index',
            compact('posts')
        );
    }

    /**
     * Tags
     *
     * @param Response $response
     *
     * @return Response
     */
    public function tags(Response $response): Response
    {
        $tags = $this->storyRepository->getAllPosts()->pluck('tags', 'id');

        $allTags   = implode(',', $tags);
        $clearTags = preg_split('/\s*,\s*/', $allTags, -1, PREG_SPLIT_NO_EMPTY);
        $tags      = array_count_values($clearTags);

        array_splice($tags, 100);

        $max     = max($tags);
        $highest = $max / 30;

        $links = [];

        $i = 0;
        foreach ($tags as $tag => $count) {
            $size = round($count / $highest);

            if ($i & 1) {
                $links[$tag] = $size;
            } else {
                $links = [$tag => $size] + $links;
            }

            $i++;
        }

        return $this->view->render(
            $response,
            'stories/tags',
            compact('links')
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
     * @param Slug     $slug
     *
     * @return Response
     */
    public function store(
        Request $request,
        Response $response,
        Slug $slug,
    ): Response {
        if (! $user = getUser()) {
            abort(403, 'Доступ запрещен!');
        }

        $input = (array) $request->getParsedBody();

        $this->validator
            ->required(['csrf', 'title', 'text'])
            ->same('csrf', $this->session->get('csrf'), 'Неверный идентификатор сессии, повторите действие!')
            ->length('title', setting('story.title_min_length'), setting('story.title_max_length'))
            ->length('text', setting('story.text_min_length'), setting('story.text_max_length'))
            ->length('tags', setting('story.tags_min_length'), setting('story.tags_max_length'));

        if ($this->validator->isValid($input)) {
            $slugify = $slug->slugify($input['title']);

            $post = Story::query()->insert([
                'user_id'    => $user->id,
                'title'      => sanitize($input['title']),
                'slug'       => $slugify,
                'text'       => sanitize($input['text']),
                'tags'       => preg_replace('/\s*,+\s*/', ',', Str::lower(sanitize(trim($input['tags'])))),
                'rating'     => 0,
                'views'      => 0,
                'created_at' => time(),
            ]);

            File::query()
                ->where('post_id', 0)
                ->where('user_id', $user->id)
                ->update(['post_id' => $post->id]);

            $this->session->set('flash', ['success' => 'Статья успешно добавлена!']);

            return $this->redirect($response, '/' . $post->getLink());
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
     * Update
     *
     * @param int      $id
     * @param Request  $request
     * @param Response $response
     * @param Slug     $slug
     *
     * @return Response
     */
    public function update(
        int $id,
        Request $request,
        Response $response,
        Slug $slug,
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
            ->length('text', setting('story.text_min_length'), setting('story.text_max_length'))
            ->length('tags', setting('story.tags_min_length'), setting('story.tags_max_length'));

        if ($this->validator->isValid($input)) {
            $slugify = $slug->slugify($input['title']);

            $post->update([
                'title' => sanitize($input['title']),
                'slug'  => $slugify,
                'text'  => sanitize($input['text']),
                'tags'  => preg_replace('/\s*,+\s*/', ',', Str::lower(sanitize(trim($input['tags'])))),
            ]);

            $this->session->set('flash', ['success' => 'Статья успешно изменена!']);

            return $this->redirect($response, '/' . $slugify . '-' . $id);
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
