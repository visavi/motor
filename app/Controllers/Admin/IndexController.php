<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Services\View;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * IndexController
 */
class IndexController extends Controller
{
    public function __construct(
        protected View $view,
    ) {}

    /**
     * Admin
     *
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function index(Request $request, Response $response): Response
    {
        return $this->view->render(
            $response,
            'admin/index',
        );
    }
}
