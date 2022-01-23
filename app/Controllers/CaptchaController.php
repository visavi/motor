<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
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
