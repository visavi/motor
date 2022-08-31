<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Comment;
use App\Models\Poll;
use App\Models\Story;
use App\Services\Session;
use App\Services\Validator;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * RatingController
 */
class RatingController extends Controller
{
    public function __construct(
        protected Session $session,
        protected Validator $validator,
    ) {}

    /**
     * Change rating
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

        $modelName = $this->getModelByType($input['type']);

        if (! $modelName) {
            return $this->json($response, [
                'success' => false,
                'message' => 'Неверный тип записи!',
            ]);
        }

        $model = $modelName::query()->find($id);

        if (! $model) {
            return $this->json($response, [
                'success' => false,
                'message' => 'Запись не найдена!',
            ]);
        }

        $this->validator
            ->required(['csrf', 'vote'])
            ->same('csrf', $this->session->get('csrf'), 'Неверный идентификатор сессии, повторите действие!')
            ->in('vote', ['+', '-'])
            ->custom($model->user_id !== getUser('id'), 'Нельзя изменять рейтинг своей записи!');

        if ($this->validator->isValid($input)) {

            $poll = Poll::query()
                ->where('entity_id', $id)
                ->where('entity_name', $input['type'])
                ->where('user_id', getUser('id'))
                ->first();

            $cancel = false;

            if ($poll) {
                if ($poll->vote === $input['vote']) {
                    return $this->json($response, [
                        'success' => false,
                    ]);
                }

                $poll->delete();
                $cancel = true;
            } else {
                Poll::query()->create([
                    'user_id'     => getUser('id'),
                    'entity_id'   => $id,
                    'entity_name' => $input['type'],
                    'vote'        => $input['vote'],
                    'created_at'  => time(),
                ]);
            }

            if ($input['vote'] === '+') {
                $rating = $model->rating + 1;
            } else {
                $rating = $model->rating - 1;
            }

            $model->rating = $rating;
            $model->save();

            return $this->json($response, [
                'success' => true,
                'cancel'  => $cancel,
                'rating'  => $model->getRating(),
            ]);
        }

        return $this->json($response, [
            'success' => false,
            'message' => current($this->validator->getErrors()),
        ]);
    }

    /**
     * Get map types
     *
     * @return string[]
     */
    private function getMapTypes(): array
    {
        return [
            'story'   => Story::class,
            'comment' => Comment::class,
        ];
    }

    /**
     * Get model by type
     *
     * @param string $type
     *
     * @return string|null
     */
    private function getModelByType(string $type): ?string
    {
        return $this->getMapTypes()[$type] ?? null;
    }
}
