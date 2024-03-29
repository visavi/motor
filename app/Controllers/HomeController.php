<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\GithubService;
use App\Services\View;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * HomeController
 */
class HomeController extends Controller
{
    public function __construct(
        protected View $view,
    ) {}

    /**
     * Main page
     *
     * @param Response $response
     * @param GithubService $githubService
     *
     * @return Response
     */
    public function index(Response $response, GithubService $githubService): Response
    {
        return $this->view->render(
            $response,
            'home/index',
            ['release' => $githubService->getLastRelease()]
        );
    }

    /**
     * Docs
     *
     * @param Response $response
     *
     * @return Response
     */
    public function docs(Response $response): Response
    {
        return $this->view->render(
            $response,
            'docs/index',
        );
    }

    /**
     * Docs pages
     *
     * @param string $page
     * @param Response $response
     *
     * @return Response
     */
    public function page(string $page, Response $response): Response
    {
        if (! $this->view->exists('docs/' . $page)) {
            abort(404, 'Страница не найдена');
        }

        return $this->view->render(
            $response,
            'docs/' . $page,
        );
    }

    /**
     * Versions
     *
     * @param Response $response
     * @param GithubService $githubService
     *
     * @return Response
     */
    public function versions(Response $response, GithubService $githubService): Response
    {
        return $this->view->render(
            $response,
            'home/versions',
            ['releases' => $githubService->getReleases()]
        );
    }

    /**
     * Commits
     *
     * @param Response $response
     * @param GithubService $githubService
     *
     * @return Response
     */
    public function commits(Response $response, GithubService $githubService): Response
    {
        return $this->view->render(
            $response,
            'home/commits',
            ['commits' => $githubService->getCommits()]
        );
    }
}
