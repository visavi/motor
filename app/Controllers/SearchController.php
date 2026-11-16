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
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function index(Request $request, Response $response): Response
    {
        $search = $request->getQueryParams()['search'] ?? '';
        $search = is_string($search) ? sanitize($search) : '';

        if ($search === '') {
            return $this->redirect($response, route('stories'));
        }

        $stories = $this->storyRepository->getStoriesBySearch($search, setting('story.per_page'));

        return $this->view->render(
            $response,
            'search/index',
            compact('stories', 'search')
        );
    }
}
