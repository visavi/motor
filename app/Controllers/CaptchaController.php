<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\Session;
use Psr\Http\Message\ResponseInterface as Response;
use Visavi\Captcha\CaptchaBuilder;

/**
 * CaptchaController
 */
class CaptchaController extends Controller
{
    public function __construct(
        protected Session $session,
    ) {}

    /**
     * Captcha
     *
     * @param Response $response
     *
     * @return Response
     */
    public function captcha(Response $response): Response
    {
        $captcha = new CaptchaBuilder();
        $this->session->set('captcha', $captcha->getPhrase());

        $response->getBody()->write($captcha->render());

        return $response->withHeader('Content-Type', 'image/gif');
    }
}
