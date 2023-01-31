<?php

declare(strict_types=1);

namespace App\Controllers;

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
     *
     * @return Response
     */
    public function index(Response $response): Response
    {
        return $this->view->render(
            $response,
            'home/index',
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
            'home/docs',
        );
    }

    /**
     * Versions
     *
     * @param Response $response
     *
     * @return Response
     */
    public function versions(Response $response): Response
    {
        return $this->view->render(
            $response,
            'home/versions',
        );
    }
}
