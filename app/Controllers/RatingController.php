<?php

declare(strict_types=1);

namespace App\Controllers;

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

        $model = Story::query()->find($id);

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
                ->where('post_id', $id)
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
                Poll::query()->insert([
                    'user_id'     => getUser('id'),
                    'post_id'     => $id,
                    'vote'        => $input['vote'],
                    'created_at'  => time(),
                ]);
            }

            if ($input['vote'] === '+') {
                $rating = $model->rating + 1;
            } else {
                $rating = $model->rating - 1;
            }

            $model->update([
                'rating' => $rating,
            ]);

            $model = Story::query()->find($id);

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
}
