<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Models\Sticker;
use App\Repositories\StickerRepository;
use App\Services\Session;
use App\Services\Validator;
use App\Services\View;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * StickerController
 */
class StickerController extends Controller
{
    public function __construct(
        protected Session $session,
        protected Validator $validator,
        protected View $view,
        protected StickerRepository $stickerRepository,
    ) {}

    /**
     * Admin
     *
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function index(Request $request, Response $response): Response
    {
        $stickers = $this->stickerRepository->getStickers(setting('sticker.per_page'));

        return $this->view->render(
            $response,
            'admin/stickers/index',
            compact('stickers')
        );
    }

    /**
     * Store
     *
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function store(
        Request $request,
        Response $response,
    ): Response {

        $input = (array) $request->getParsedBody();
        $files = $request->getUploadedFiles();
        $input = array_merge($input, $files);

        $this->validator
            ->required(['csrf', 'code', 'file'])
            ->same('csrf', $this->session->get('csrf'), 'Неверный идентификатор сессии, повторите действие!')
            ->length('code', 2, 20)
            ->custom(str_starts_with($input['code'], ':'), 'Код стикера должен начинаться с двоеточия!')
            ->file('file', [
                'size_max'   => setting('sticker.size_max'),
                'weight_max' => setting('sticker.weight_max'),
                'weight_min' => setting('sticker.weight_min'),
            ]);

        $stickerExists = Sticker::query()->where('code', 'lax', $input['code'])->first();
        if ($stickerExists) {
            $this->validator->addError('code', 'Данный стикер уже существует!');
        }

        if ($this->validator->isValid($input)) {
            $file      = $input['file'];
            $filename  = sanitize($file->getClientFilename());
            $extension = getExtension($filename);
            $path      = (new Sticker)->uploadPath . '/' . uniqueName($extension);

            $file->moveTo(publicPath($path));

            $this->stickerRepository->create([
                'code'  => sanitize($input['code']),
                'path'  => $path,
            ]);

            $this->session->set('flash', ['success' => 'Стикер успешно добавлен!']);
        } else {
            $this->session->set('flash', ['errors' => $this->validator->getErrors(), 'old' => $input]);
        }

        return $this->redirect($response, route('admin-stickers'));
    }

    /**
     * Edit
     *
     * @param int      $id
     * @param Response $response
     *
     * @return Response
     */
    public function edit(int $id, Response $response): Response
    {
        $sticker = $this->stickerRepository->getById($id);
        if (! $sticker) {
            abort(404, 'Стикер не найден!');
        }

        return $this->view->render(
            $response,
            'admin/stickers/edit',
            compact('sticker')
        );
    }

    /**
     * Update
     *
     * @param int      $id
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function update(
        int $id,
        Request $request,
        Response $response,
    ): Response
    {
        $sticker = $this->stickerRepository->getById($id);
        if (! $sticker) {
            abort(404, 'Стикер не найден!');
        }

        $input = (array) $request->getParsedBody();

        $this->validator
            ->required(['csrf', 'code'])
            ->same('csrf', $this->session->get('csrf'), 'Неверный идентификатор сессии, повторите действие!')
            ->length('code', 2, 20)
            ->custom(str_starts_with($input['code'], ':'), 'Код стикера должен начинаться с двоеточия!');

        if ($this->validator->isValid($input)) {
            $sticker->update([
                'code' => sanitize($input['code']),
            ]);
        } else {
            $this->session->set('flash', ['errors' => $this->validator->getErrors(), 'old' => $input]);

            return $this->redirect($response, route('admin-sticker-edit', ['id' => $sticker->id]));
        }

        $this->session->set('flash', ['success' => 'Стикер успешно изменен!']);

        return $this->redirect($response, route('admin-stickers'));
    }

    /**
     * Destroy
     *
     * @param int $id
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function destroy(int $id, Request $request, Response $response): Response
    {
        $sticker = $this->stickerRepository->getById($id);
        if (! $sticker) {
            abort(404, 'Стикер не найден!');
        }

        $input = (array) $request->getParsedBody();

        $this->validator
            ->required('csrf')
            ->same('csrf', $this->session->get('csrf'), 'Неверный идентификатор сессии, повторите действие!');

        if ($this->validator->isValid($input)) {
            if (file_exists(publicPath($sticker->path))) {
                unlink(publicPath($sticker->path));
            }

            $sticker->delete();

            $this->session->set('flash', ['success' => 'Стикер успешно удален!']);
        } else {
            $this->session->set('flash', ['errors' => $this->validator->getErrors()]);
        }

        return $this->redirect($response, route('admin-stickers'));
    }
}
