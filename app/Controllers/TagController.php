<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\StoryRepository;
use App\Services\TagCloud;
use App\Services\View;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * FavoriteController
 */
class TagController extends Controller
{
    public function __construct(
        protected View $view,
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
        $tags = $this->storyRepository->getPopularTags(100);

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
}
