<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\StoryRepository;
use App\Services\View;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * SearchController
 */
class SearchController extends Controller
{
    public function __construct(
        protected View $view,
        protected StoryRepository $storyRepository,
    ) {}

    /**
     * Modal stickers
     *
     * @param Response $response
     *
     * @return Response
     */
    public function index(Request $request, Response $response): Response
    {
        $query  = $request->getQueryParams();
        $search = urldecode($query['search'] ?? '');

        $stories = $this->storyRepository->getStoriesBySearch($search, setting('story.per_page'));

        return $this->view->render(
            $response,
            'stories/index',
            compact('stories', 'search')
        );
    }
}
