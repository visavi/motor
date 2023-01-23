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

    public function index(Response $response): Response
    {
        return $this->view->render(
            $response,
            'home/index',
        );
    }
}
