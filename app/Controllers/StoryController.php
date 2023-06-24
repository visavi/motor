<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\File;
use App\Models\Story;
use App\Models\Tag;
use App\Models\User;
use App\Repositories\FileRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\ReadRepository;
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

        if (! $story->active && $story->user_id !== getUser('id')) {
            abort(403, 'Статья еще не опубликована!');
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
     * Create
     *
     * @param Response $response
     *
     * @return Response
     */
    public function create(Response $response): Response
    {
        if (! setting('story.allow_posting') && ! isAdmin()) {
            abort(403, 'Публикация статей запрещена администратором!');
        }

        $user  = getUser();
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
        NotificationRepository $notificationRepository,
    ): Response {
        $user    = getUser();
        $input   = (array) $request->getParsedBody();
        $tags    = array_map('sanitize', $input['tags'] ?? []);
        $created = time();

        $this->validator
            ->required(['csrf', 'title', 'text', 'tags'])
            ->same('csrf', $this->session->get('csrf'), 'Неверный идентификатор сессии, повторите действие!')
            ->length('title', setting('story.title_min_length'), setting('story.title_max_length'))
            ->length('text', setting('story.text_min_length'), setting('story.text_max_length'))
            ->custom(count($tags) <= setting('story.tags_max'), ['tags' => 'Превышено максимальное количество тегов!'])
            ->boolean('locked')
            ->boolean('active')
            ->boolean('delay');

        foreach ($tags as $tag) {
            $this->validator->custom(
                Str::length($tag) >= setting('story.tags_min_length') && Str::length($tag) <= setting('story.tags_max_length'),
                ['tags' => sprintf('Длина тегов должна быть от %d до %d символов!', setting('story.tags_min_length'), setting('story.tags_max_length'))]
            );
        }

        if (! empty($input['delay']) && isAdmin()) {
            $created = ! empty($input['created']) ? strtotime($input['created']) : 0;
            $this->validator->custom(
                $created > time(),
                ['created' => 'Дата отложенной публикации должна быть больше текущего времени!']
            );
        }

        if ($this->validator->isValid($input)) {
            $title = sanitize($input['title']);

            $story = Story::query()->create([
                'user_id'    => $user->id,
                'title'      => $title,
                'slug'       => $slug->slugify($input['title']),
                'text'       => sanitize($input['text']),
                'rating'     => 0,
                'reads'      => 0,
                'active'     => isAdmin() ? $input['active'] ?? true : setting('story.active'),
                'locked'     => isAdmin() ? $input['locked'] ?? false : false,
                'created_at' => $created,
            ]);

            foreach ($tags as $value) {
                Tag::query()->create([
                    'story_id' => $story->id,
                    'tag'      => Str::lower($value),
                ]);
            }

            // Attach files to story
            File::query()
                ->where('story_id', 0)
                ->where('user_id', $user->id)
                ->update(['story_id' => $story->id]);

            // Send notify to admins
            if (! isAdmin() && ! setting('story.active')) {
                $admins = User::query()->whereIn('role', User::ADMIN_ROLES)->get();

                $notifyText = sprintf('Пользователь @%s добавил новую статью [b][url=%s]%s[/url][/b]',
                    $user->login,
                    $story->getLink(),
                    $title
                );

                foreach ($admins as $admin) {
                    $notificationRepository->createNotification($admin, $notifyText);
                }
            }

            $this->session->set('flash', ['success' => 'Статья успешно добавлена!']);

            return $this->redirect($response, $story->getLink());
        }

        $this->session->set('flash', ['errors' => $this->validator->getErrors(), 'old' => $input]);

        return $this->redirect($response, route('story-create'));
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
        $user = getUser();

        $story = $this->storyRepository->getById($id);
        if (! $story) {
            abort(404, 'Статья не найдена!');
        }

        if ($story->user_id !== $user->id && ! isAdmin()) {
            abort(403, 'Вы не являетесь автором данной записи!');
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
        $user = getUser();

        $story = $this->storyRepository->getById($id);
        if (! $story) {
            abort(404, 'Статья не найдена!');
        }

        if ($story->user_id !== $user->id && ! isAdmin()) {
            abort(403, 'Вы не являетесь автором данной записи!');
        }

        $input = (array) $request->getParsedBody();
        $tags = array_map('sanitize', $input['tags'] ?? []);

        $this->validator
            ->required(['csrf', 'title', 'text', 'tags'])
            ->same('csrf', $this->session->get('csrf'), 'Неверный идентификатор сессии, повторите действие!')
            ->length('title', setting('story.title_min_length'), setting('story.title_max_length'))
            ->length('text', setting('story.text_min_length'), setting('story.text_max_length'))
            ->custom(count($tags) <= setting('story.tags_max'), ['tags' => 'Превышено максимальное количество тегов'])
            ->boolean('locked')
            ->boolean('active')
            ->boolean('delay');

        foreach ($tags as $tag) {
            $this->validator->custom(
                Str::length($tag) >= setting('story.tags_min_length') && Str::length($tag) <= setting('story.tags_max_length'),
                ['tags' => sprintf('Длина тегов должна быть от %d до %d символов!', setting('story.tags_min_length'), setting('story.tags_max_length'))]
            );
        }

        if (! empty($input['delay']) && isAdmin()) {
            $created = ! empty($input['created']) ? strtotime($input['created']) : 0;
            $this->validator->custom(
                $created > time(),
                ['created' => 'Дата отложенной публикации должна быть больше текущего времени!']
            );
        }

        if ($this->validator->isValid($input)) {
            $story->update([
                'title'      => sanitize($input['title']),
                'slug'       => $slug->slugify($input['title']),
                'text'       => sanitize($input['text']),
                'active'     => isAdmin() ? $input['active'] ?? $story->active : $story->active,
                'locked'     => isAdmin() ? $input['locked'] ?? $story->locked : $story->locked,
                'created_at' => $created ?? $story->created_at,
            ]);

            Tag::query()->where('story_id', $story->id)->delete();

            foreach ($tags as $value) {
                Tag::query()->create([
                    'story_id' => $story->id,
                    'tag'      => Str::lower($value),
                ]);
            }

            $story->refresh();
            $this->session->set('flash', ['success' => 'Статья успешно изменена!']);

            return $this->redirect($response, $story->getLink());
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
        $user = getUser();

        $story = $this->storyRepository->getById($id);
        if (! $story) {
            abort(404, 'Статья не найдена');
        }

        if ($story->user_id !== $user->id && ! isAdmin()) {
            abort(403, 'Вы не являетесь автором данной записи!');
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

        return $this->redirect($response, route('stories'));
    }
}
