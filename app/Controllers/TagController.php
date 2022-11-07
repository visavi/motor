<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Tag;
use App\Repositories\StoryRepository;
use App\Repositories\TagRepository;
use App\Services\TagCloud;
use App\Services\View;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * FavoriteController
 */
class TagController extends Controller
{
    public function __construct(
        protected View $view,
        protected TagRepository $tagRepository,
        protected StoryRepository $storyRepository,
    ) {}

    /**
     * Tags
     *
     * @param Response $response
     * @param TagCloud $tagCloud
     *
     * @return Response
     */
    public function index(Response $response, TagCloud $tagCloud): Response
    {
        $tags = $this->tagRepository->getPopularTags(100);

        $tags = $tagCloud->generate($tags);

        return $this->view->render(
            $response,
            'tags/tags',
            compact('tags')
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
    public function search(string $tag, Response $response): Response
    {
        $tag = urldecode(escape($tag));

        $stories = $this->storyRepository->getStoriesByTag($tag, setting('story.per_page'));

        return $this->view->render(
            $response,
            'tags/index',
            compact('stories', 'tag')
        );
    }

    /**
     * Search by tag
     *
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
/*        public function tag(Request $request, Response $response): Response
        {
            $query  = $request->getQueryParams();
            $search = urldecode(escape($query['query'] ?? ''));

            if (! $search) {
                return $this->json($response, []);
            }

            $tags = $this->storyRepository->getAllTags();

            $arrayTags = [];

            foreach ($tags as $tag) {
                $arrayTags[] = explode(',', $tag);
            }

            $flatten = [];
            array_walk_recursive($arrayTags, static function ($value) use (&$flatten) {
                $flatten[] = $value;
            });

            $results = array_filter($flatten, static function ($value) use ($search) {
                return mb_stripos($value, $search, 0, 'UTF-8') !== false;
            });

            $namedTags = [];
            foreach ($results as $result) {
                $namedTags[] = ['value' => $result, 'label' => $result];
            }

            return $this->json($response, $namedTags);
        }*/

    /**
     * Search by tag
     *
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function tag(Request $request, Response $response): Response
    {
        $query  = $request->getQueryParams();
        $search = urldecode(escape($query['query'] ?? ''));

        if (! $search) {
            return $this->json($response, []);
        }

        $tags = Tag::query()->where('tag', 'like', $search . '%')->limit(10)->get();
        $tags = array_unique($tags->pluck('tag'));

        $namedTags = [];
        foreach ($tags as $tag) {
            $namedTags[] = ['value' => $tag, 'label' => $tag];
        }

        return $this->json($response, $namedTags);
    }
}
