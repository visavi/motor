<?php

namespace App\Controllers;

use App\Models\Guestbook;
use App\Services\Validator;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Throwable;

/**
 * GuestbookController
 */
class GuestbookController extends Controller
{
    /**
     * Index
     *
     * @param Response $response
     *
     * @return Response
     * @throws Throwable
     */
    public function index(Response $response): Response
    {
        $total = Guestbook::query()->count();
        $paginator = $this->paginator->create($total);

        $messages = Guestbook::query()
            ->reverse()
            ->offset($paginator->offset)
            ->limit($paginator->limit)
            ->get();

        return $this->view->render(
            $response,
            'guestbook/index.twig',
            compact('messages', 'paginator')
        );
    }

    /**
     * Create
     *
     * @param Request   $request
     * @param Response  $response
     * @param Validator $validator
     *
     * @return Response
     */
    public function create(Request $request, Response $response, Validator $validator): Response
    {
        $input = (array) $request->getParsedBody();
        $files = $request->getUploadedFiles();
        $input = array_merge($input, $files);

        $validator
            ->required(['name', 'title', 'text', 'image'])
            ->length('title', 5, 100)
            ->length(['name', 'text'], 5, 1000)
            ->file('image', [
                'size_max'   => 500000,
                'weight_min' => 100,
            ]);

        if ($validator->isValid($input)) {
            Guestbook::query()->insert([
                'name'  => sanitize($input['name']),
                'title' => sanitize($input['title']),
                'text'  => sanitize($input['text']),
                'time'  => time(),
            ]);
        } else {
            $this->session->set('flash', ['errors' => $validator->getErrors(), 'old' => $input]);
        }

        return $response->withHeader('Location', '/guestbook');
    }

    /**
     * Edit
     *
     * @param int      $id
     * @param Response $response
     *
     * @return Response
     * @throws Throwable
     */
    public function edit(int $id, Response $response): Response
    {
        $message = Guestbook::query()->find($id);
        if (! $message) {
            echo 'Сообщение не найдено'; //TODO abort
        }

        return $this->view->render(
            $response,
            'guestbook/edit.twig',
            compact('message')
        );
    }

    /**
     * Store
     *
     * @param int       $id
     * @param Request   $request
     * @param Response  $response
     * @param Validator $validator
     *
     * @return Response
     */
    public function store(int $id, Request $request, Response $response, Validator $validator): Response
    {
        $message = Guestbook::query()->find($id);
        if (! $message) {
            echo 'Сообщение не найдено'; //TODO abort
        }

        $input = (array) $request->getParsedBody();

        $validator
            ->required(['name', 'title', 'text'])
            ->length('title', 5, 100)
            ->length(['name', 'text'], 5, 1000);

        if ($validator->isValid($input)) {
            $message->update([
                'name'  => sanitize($input['name']),
                'title' => sanitize($input['title']),
                'text'  => sanitize($input['text']),
            ]);
        } else {
            $this->session->set('flash', ['errors' => $validator->getErrors(), 'old' => $input]);

            return $response->withHeader('Location', '/guestbook/' . $id . '/edit');
        }

        $this->session->set('flash', ['success' => 'Сообщение успешно изменено!']);

        return $response->withHeader('Location', '/guestbook');
    }

    /**
     * Delete
     *
     * @param int      $id
     * @param Response $response
     *
     * @return Response
     */
    public function delete(int $id, Response $response): Response
    {
        $message = Guestbook::query()->find($id);
        $message?->delete();

        $this->session->set('flash', ['success' => 'Сообщение успешно удалено!']);

        return $response->withHeader('Location', '/guestbook');
    }
}
