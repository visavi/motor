<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Favorite;
use App\Models\Poll;
use App\Models\Story;
use App\Services\Session;
use App\Services\Validator;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * FavoriteController
 */
class FavoriteController extends Controller
{
    public function __construct(
        protected Session $session,
        protected Validator $validator,
    ) {}

    /**
     * Add/delete to favorite
     *
     * @param int      $id
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function change(int $id, Request $request, Response $response): Response
    {
        $input = (array) $request->getParsedBody();

        $model = Story::query()->find($id);

        if (! $model) {
            return $this->json($response, [
                'success' => false,
                'message' => 'Запись не найдена!',
            ]);
        }

        $this->validator
            ->required(['csrf'])
            ->same('csrf', $this->session->get('csrf'), 'Неверный идентификатор сессии, повторите действие!');

        if ($this->validator->isValid($input)) {
            $favorite = Favorite::query()
                ->where('story_id', $id)
                ->where('user_id', getUser('id'))
                ->first();

            if ($favorite) {
                $favorite->delete();

                return $this->json($response, [
                    'success' => true,
                    'type'    => 'delete',
                    'message' => 'Статья успешно удалена из избранного!',
                ]);
            }

            Favorite::query()->insert([
                'user_id'     => getUser('id'),
                'story_id'    => $id,
                'created_at'  => time(),
            ]);

            return $this->json($response, [
                'success' => true,
                'type'    => 'add',
                'message' => 'Статья успешно добавлена в избранное!',
            ]);
        }

        return $this->json($response, [
            'success' => false,
            'message' => current($this->validator->getErrors()),
        ]);
    }
}
