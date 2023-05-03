<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\View;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * BBCodeController
 */
class BBCodeController extends Controller
{
    public function __construct(
        protected View $view,
    ) {}

    /**
     * Parse BBCode
     *
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function bbcode(Request $request, Response $response): Response
    {
        $input = (array) $request->getParsedBody();
        $message = $input['data'] ?? '';

        return $this->view->render(
            $response,
            'app/_bbcode',
            compact('message')
        );
    }
}
