<?php

declare(strict_types=1);

namespace App\Controllers\User;

use App\Controllers\Controller;
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
    public function __construct(
        protected View $view,
        protected Session $session,
    ) {}

    /**
     * Profile
     *
     * @param Response $response
     *
     * @return Response
     */
    public function index(Response $response): Response
    {
        if (! $user = getUser()) {
            abort(403, 'Для выполнения действия необходимо авторизоваться!');
        }

        return $this->view->render(
            $response,
            'profile/profile',
            compact('user'),
        );
    }

    /**
     * Store
     *
     * @param Request      $request
     * @param Response     $response
     * @param Validator    $validator
     * @param ImageManager $manager
     *
     * @return Response
     */
    public function store(
        Request $request,
        Response $response,
        Validator $validator,
        ImageManager $manager,
    ): Response {
        if (! $user = getUser()) {
            abort(403, 'Для выполнения действия необходимо авторизоваться!');
        }

        $input = (array) $request->getParsedBody();
        $files = $request->getUploadedFiles();
        $input = array_merge($input, $files);

        $validator
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

        if ($validator->isValid($input)) {
            if ($input['picture']->getError() === UPLOAD_ERR_OK) {
                // Удаляем старое фото
                if ($user->picture && file_exists(publicPath($user->picture))) {
                    unlink(publicPath($user->picture));
                }

                if ($user->avatar && file_exists(publicPath($user->avatar))) {
                    unlink(publicPath($user->avatar));
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

                $user->update([
                    'picture' => $picturePath,
                    'avatar'  => $avatarPath,
                ]);
            }

            $user->update([
                'email' => sanitize($input['email']),
                'name'  => sanitize($input['name']),
            ]);

            $this->session->set('flash', ['success' => 'Данные успешно изменены!']);
        } else {
            $this->session->set('flash', ['errors' => $validator->getErrors(), 'old' => $input]);
        }

        return $this->redirect($response, '/profile');
    }
}
