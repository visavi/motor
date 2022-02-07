<?php

declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Visavi\Captcha\CaptchaBuilder;

/**
 * CaptchaController
 */
class CaptchaController extends Controller
{
    public function captcha(Response $response): Response
    {
        $captcha = new CaptchaBuilder();
        $this->session->set('captcha', $captcha->getPhrase());

        $response->getBody()->write($captcha->render());

        return $response->withHeader('Content-Type', 'image/gif');
    }
}
