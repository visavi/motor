<?php

declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;

/**
 * StickerController
 */
class StickerController extends Controller
{
    public function modal(Response $response): Response
    {
        $stickers = glob(publicPath('/uploads/stickers/*.{gif,png,jpg,jpeg}'), GLOB_BRACE);

        $view = $this->view->fetch(
            'stickers/_modal',
            compact('stickers')
        );

        $response->getBody()->write(json_encode([
            'success' => true,
            'stickers' => $view,
        ],
            JSON_THROW_ON_ERROR
        ));

        return $response->withHeader('Content-Type', 'application/json');
    }
}
