<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Guestbook;
use App\Repositories\GuestbookRepository;
use App\Services\Session;
use App\Services\Validator;
use App\Services\View;
use Intervention\Image\Constraint;
use Intervention\Image\ImageManager;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\UploadedFileInterface;

/**
 * GuestbookController
 */
class GuestbookController extends Controller
{
    public function __construct(
        protected View $view,
        protected Session $session,
        protected Validator $validator,
        protected GuestbookRepository $guestbookRepository,
    ) {}

    /**
     * Index
     *
     * @param Response $response
     *
     * @return Response
     */
    public function index(Response $response): Response
    {
        $messages = $this->guestbookRepository->getMessages(setting('guestbook.per_page'));

        return $this->view->render(
            $response,
            'guestbook/index',
            compact('messages')
        );
    }

    /**
     * Create
     *
     * @param Request      $request
     * @param Response     $response
     * @param ImageManager $manager
     *
     * @return Response
     */
    public function create(
        Request $request,
        Response $response,
        ImageManager $manager,
    ): Response {
        if (! isUser() && ! setting('guestbook.allow_guests')) {
            abort(403, 'Доступ запрещен!');
        }

        $input = (array) $request->getParsedBody();
        $files = $request->getUploadedFiles();
        $input = array_merge($input, $files);

        $this->validator
            ->required(['csrf', 'title', 'text'])
            ->same('csrf', $this->session->get('csrf'), 'Неверный идентификатор сессии, повторите действие!')
            ->length('title', setting('guestbook.title_min_length'), setting('guestbook.title_max_length'))
            ->length('text', setting('guestbook.text_min_length'), setting('guestbook.text_max_length'))
            ->file('image', [
                'size_max'   => setting('file.size_max'),
                'weight_max' => setting('image.weight_max'),
                'weight_min' => setting('image.weight_min'),
            ]);

        if (! isUser()) {
            $this->validator
                ->required('captcha')
                ->same('captcha', $this->session->get('captcha'), 'Не удалось пройти проверку captcha!');
        }

        if ($this->validator->isValid($input)) {
            if (
                isset($input['image'])
                && $input['image'] instanceof UploadedFileInterface
                && $input['image']->getError() === UPLOAD_ERR_OK
            ) {
                $extension = getExtension($input['image']->getClientFilename());
                $path = '/uploads/guestbook/' . uniqueName($extension);

                $img = $manager->make($input['image']->getFilePath());
                $img->resize(setting('image.resize'), setting('image.resize'), static function (Constraint $constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });

                $img->save(publicPath($path));
            }

            $login = isUser() ? $this->session->get('login') : null;

            Guestbook::query()->insert([
                'login'      => $login,
                'title'      => sanitize($input['title']),
                'text'       => sanitize($input['text']),
                'image'      => $path ?? null,
                'created_at' => time(),
            ]);

            $this->session->set('flash', ['success' => 'Сообщение успешно добавлено!']);
        } else {
            $this->session->set('flash', ['errors' => $this->validator->getErrors(), 'old' => $input]);
        }

        return $this->redirect($response, '/guestbook');
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
        if (! isAdmin()) {
            abort(403, 'Доступ запрещен!');
        }

        $message = $this->guestbookRepository->getById($id);
        if (! $message) {
            abort(404, 'Сообщение не найдено');
        }

        return $this->view->render(
            $response,
            'guestbook/edit',
            compact('message')
        );
    }

    /**
     * Store
     *
     * @param int          $id
     * @param Request      $request
     * @param Response     $response
     * @param ImageManager $manager
     *
     * @return Response
     */
    public function store(
        int $id,
        Request $request,
        Response $response,
        ImageManager $manager,
    ): Response
    {
        if (! isAdmin()) {
            abort(403, 'Доступ запрещен!');
        }

        $message = $this->guestbookRepository->getById($id);
        if (! $message) {
            abort(404, 'Сообщение не найдено');
        }

        $input = (array) $request->getParsedBody();
        $files = $request->getUploadedFiles();
        $input = array_merge($input, $files);

        $this->validator
            ->required(['csrf', 'title', 'text'])
            ->same('csrf', $this->session->get('csrf'), 'Неверный идентификатор сессии, повторите действие!')
            ->length('title', setting('guestbook.title_min_length'), setting('guestbook.title_max_length'))
            ->length('text', setting('guestbook.text_min_length'), setting('guestbook.text_max_length'))
            ->file('image', [
                'size_max'   => setting('file.size_max'),
                'weight_max' => setting('image.weight_max'),
                'weight_min' => setting('image.weight_min'),
            ]);

        if ($this->validator->isValid($input)) {
            // Удаляем старое фото
            if (
                isset($input['delete_image'])
                && $message->image
                && file_exists(publicPath($message->image))
            ) {
                $path = '';
                unlink(publicPath($message->image));
            }

            // Загрузка фото
            if (
                isset($input['image'])
                && $input['image'] instanceof UploadedFileInterface
                && $input['image']->getError() === UPLOAD_ERR_OK
            ) {
                // Удаляем старое фото
                if ($message->image && file_exists(publicPath($message->image))) {
                    unlink(publicPath($message->image));
                }

                $extension = getExtension($input['image']->getClientFilename());
                $path = '/uploads/guestbook/' . uniqueName($extension);

                $img = $manager->make($input['image']->getFilePath());
                $img->resize(setting('image.resize'), setting('image.resize'), static function (Constraint $constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });

                $img->save(publicPath($path));
            }

            $message->update([
                'title' => sanitize($input['title']),
                'text'  => sanitize($input['text']),
                'image' => $path ?? $message->image,
            ]);
        } else {
            $this->session->set('flash', ['errors' => $this->validator->getErrors(), 'old' => $input]);

            return $this->redirect($response, '/guestbook/' . $id . '/edit');
        }

        $this->session->set('flash', ['success' => 'Сообщение успешно изменено!']);

        return $this->redirect($response, '/guestbook');
    }

    /**
     * Destroy
     *
     * @param int       $id
     * @param Request   $request
     * @param Response  $response
     *
     * @return Response
     */
    public function destroy(int $id, Request $request, Response $response): Response
    {
        if (! isAdmin()) {
            abort(403, 'Доступ запрещен!');
        }

        $message = $this->guestbookRepository->getById($id);
        if (! $message) {
            abort(404, 'Сообщение не найдено');
        }

        $input = (array) $request->getParsedBody();

        $this->validator
            ->required('csrf')
            ->same('csrf', $this->session->get('csrf'), 'Неверный идентификатор сессии, повторите действие!');

        if ($this->validator->isValid($input)) {
            $message->delete();

            if ($message->image && file_exists(publicPath($message->image))) {
                unlink(publicPath($message->image));
            }

            $this->session->set('flash', ['success' => 'Сообщение успешно удалено!']);
        } else {
            $this->session->set('flash', ['errors' => $this->validator->getErrors()]);
        }

        return $this->redirect($response, '/guestbook');
    }
}
