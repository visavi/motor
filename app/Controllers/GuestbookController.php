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
        $data = (array) $request->getParsedBody();
        $uploadedFiles = $request->getUploadedFiles();
        $data = array_merge($data, $uploadedFiles);

        $validator
            ->required(['name', 'title', 'text', 'image'])
            ->length('title', 5, 100)
            ->length(['name', 'text'], 5, 1000)
            ->file('image', [
                'size_max'   => 500000,
                'weight_min' => 100,
            ]);
            //->add('text', fn ($input) => str_starts_with($input, '-'), 'Текст должен начинаться со знака дефис!');

        if ($validator->isValid($data)) {
            Guestbook::query()->insert([
                'name'  => sanitize($data['name']),
                'title' => sanitize($data['title']),
                'text'  => sanitize($data['text']),
                'time'  => time(),
            ]);
        } else {
            var_dump($validator->getErrors()); exit;
            //setInput($request->all());
            //setFlash('danger', $validator->getErrors());
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

        $data = (array) $request->getParsedBody();

        $validator
            ->required(['name', 'title', 'text'])
            ->length('title', 5, 100)
            ->length(['name', 'text'], 5, 1000);

        if ($validator->isValid($data)) {
            $message->update([
                'name'  => sanitize($data['name']),
                'title' => sanitize($data['title']),
                'text'  => sanitize($data['text']),
            ]);
        } else {

        }

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

        return $response->withHeader('Location', '/guestbook');
    }
}
