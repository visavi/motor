<?php

declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * BBCodeController
 */
class BBCodeController extends Controller
{
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

        $response->getBody()->write(bbCode($input['data'] ?? ''));

        return $response;
    }
}
