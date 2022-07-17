<?php

declare(strict_types=1);

namespace App\Controllers\User;

use App\Controllers\Controller;
use App\Models\User;
use App\Services\Session;
use App\Services\Validator;
use App\Services\View;
use Intervention\Image\Constraint;
use Intervention\Image\ImageManager;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * ProfileController
 */
class ProfileController extends Controller
{
    protected User $user;

    public function __construct(
        protected View $view,
        protected Session $session,
        protected Validator $validator,
    ) {
        $this->user = getUser();
    }

    /**
     * Profile
     *
     * @param Response $response
     *
     * @return Response
     */
    public function index(Response $response): Response
    {
        return $this->view->render(
            $response,
            'profile/profile',
            ['user' => $this->user],
        );
    }

    /**
     * Store
     *
     * @param Request      $request
     * @param Response     $response
     * @param ImageManager $manager
     *
     * @return Response
     */
    public function store(
        Request $request,
        Response $response,
        ImageManager $manager,
    ): Response {
        $input = (array) $request->getParsedBody();
        $files = $request->getUploadedFiles();
        $input = array_merge($input, $files);

        $this->validator
            ->required(['csrf', 'email'])
            ->same('csrf', $this->session->get('csrf'), 'Неверный идентификатор сессии, повторите действие!')
            ->length('email', 5, 100)
            ->email('email')
            ->length('name', 3, 20)
            ->file('picture', [
                'size_max'   => setting('file.size_max'),
                'weight_max' => setting('image.weight_max'),
                'weight_min' => setting('image.weight_min'),
            ]);

        if ($this->validator->isValid($input)) {
            if ($input['picture']->getError() === UPLOAD_ERR_OK) {
                // Удаляем старое фото
                if ($this->user->picture && file_exists(publicPath($this->user->picture))) {
                    unlink(publicPath($this->user->picture));
                }

                if ($this->user->avatar && file_exists(publicPath($this->user->avatar))) {
                    unlink(publicPath($this->user->avatar));
                }

                $extension = getExtension($input['picture']->getClientFilename());
                $picturePath = '/uploads/pictures/' . uniqueName($extension);

                $img = $manager->make($input['picture']->getFilePath());
                $img->resize(setting('image.resize'), setting('image.resize'), static function (Constraint $constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });

                $img->save(publicPath($picturePath));

                $avatarPath = '/uploads/avatars/' . uniqueName('png');
                $img = $manager->make($input['picture']->getFilePath());
                $img->fit(64);
                $img->save(publicPath($avatarPath));

                $this->user->update([
                    'picture' => $picturePath,
                    'avatar'  => $avatarPath,
                ]);
            }

            $this->user->update([
                'email' => sanitize($input['email']),
                'name'  => sanitize($input['name']),
            ]);

            $this->session->set('flash', ['success' => 'Данные успешно изменены!']);
        } else {
            $this->session->set('flash', ['errors' => $this->validator->getErrors(), 'old' => $input]);
        }

        return $this->redirect($response, '/profile');
    }

    /**
     * Delete photo
     *
     * @param Request  $request
     * @param Response $response
     * @return Response
     */
    public function deletePhoto(Request $request, Response $response): Response
    {
        $input = (array) $request->getParsedBody();
        $this->validator
            ->required('csrf')
            ->same('csrf', $this->session->get('csrf'), 'Неверный идентификатор сессии, повторите действие!');

        if ($this->validator->isValid($input)) {
            // Удаляем старое фото
            if ($this->user->picture && file_exists(publicPath($this->user->picture))) {
                unlink(publicPath($this->user->picture));
            }

            if ($this->user->avatar && file_exists(publicPath($this->user->avatar))) {
                unlink(publicPath($this->user->avatar));
            }

            $this->user->update([
                'picture' => '',
                'avatar'  => '',
            ]);

            $this->session->set('flash', ['success' => 'Фото успешно удалено!']);
        } else {
            $this->session->set('flash', ['errors' => $this->validator->getErrors()]);
        }

        return $this->redirect($response, '/profile');
    }
}
