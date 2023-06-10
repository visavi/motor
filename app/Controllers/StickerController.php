<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Sticker;
use App\Services\View;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * StickerController
 */
class StickerController extends Controller
{
    public function __construct(
        protected View $view,
    ) {}

    /**
     * Modal stickers
     *
     * @param Response $response
     *
     * @return Response
     */
    public function modal(Response $response): Response
    {
        $stickers = Sticker::query()
            ->get()
            ->pluck('path', 'code')
            ->all();

        $view = $this->view->fetch(
            'stickers/_modal',
            compact('stickers')
        );

        return $this->json($response, [
            'success' => true,
            'stickers' => $view,
        ]);
    }
}
