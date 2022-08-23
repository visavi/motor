<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\File;
use App\Models\Story;
use App\Repositories\FileRepository;
use App\Repositories\ReadRepository;
use App\Repositories\StoryRepository;
use App\Services\Session;
use App\Services\Slug;
use App\Services\Str;
use App\Services\TagCloud;
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
        protected ReadRepository $readRepository,
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
        $stories = $this->storyRepository->getStories(setting('story.per_page'));

        return $this->view->render(
            $response,
            'stories/index',
            compact('stories')
        );
    }

    /**
     * View
     *
     * @param string   $slug
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function view(string $slug, Request $request, Response $response): Response
    {
        $story = $this->storyRepository->getBySlug($slug);
        if (! $story) {
            abort(404, 'Статья не найдена!');
        }

        // Count reads
        $this->readRepository->createRead($story, $request->getAttribute('ip'));

        $files = $this->fileRepository->getFilesByStoryId($story->id);

        return $this->view->render(
            $response,
            'stories/view',
            compact('story', 'files')
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
        $stories = $this->storyRepository->getStoriesByTag(urldecode($tag), setting('story.per_page'));

        return $this->view->render(
            $response,
            'stories/index',
            compact('stories')
        );
    }

    /**
     * Tags
     *
     * @param Response $response
     * @param TagCloud $tagCloud
     *
     * @return Response
     */
    public function tags(Response $response, TagCloud $tagCloud): Response
    {
        $tags = $this->storyRepository->getPopularTags(100);

        $tags = $tagCloud->generate($tags);

        return $this->view->render(
            $response,
            'stories/tags',
            compact('tags')
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

            $story = Story::query()->insert([
                'user_id'    => $user->id,
                'title'      => sanitize($input['title']),
                'slug'       => $slugify,
                'text'       => sanitize($input['text']),
                'tags'       => preg_replace('/\s*,+\s*/', ',', Str::lower(sanitize(trim($input['tags'])))),
                'rating'     => 0,
                'reads'      => 0,
                'created_at' => time(),
            ]);

            File::query()
                ->where('story_id', 0)
                ->where('user_id', $user->id)
                ->update(['story_id' => $story->id]);

            $this->session->set('flash', ['success' => 'Статья успешно добавлена!']);

            return $this->redirect($response, $story->getLink());
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

        $story = $this->storyRepository->getById($id);
        if (! $story) {
            abort(404, 'Статья не найдена!');
        }

        $files = $this->fileRepository->getFilesByStoryId($story->id);

        return $this->view->render(
            $response,
            'stories/edit',
            compact('story', 'files')
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

        $story = $this->storyRepository->getById($id);
        if (! $story) {
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

            $story->update([
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

        $story = $this->storyRepository->getById($id);
        if (! $story) {
            abort(404, 'Статья не найдена');
        }

        $input = (array) $request->getParsedBody();

        $this->validator
            ->required('csrf')
            ->same('csrf', $this->session->get('csrf'), 'Неверный идентификатор сессии, повторите действие!');

        if ($this->validator->isValid($input)) {
            $story->delete();

            $this->session->set('flash', ['success' => 'Статья успешно удалена!']);
        } else {
            $this->session->set('flash', ['errors' => $this->validator->getErrors()]);
        }

        return $this->redirect($response, '/');
    }
}
