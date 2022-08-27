<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\File;
use App\Models\Story;
use App\Services\Session;
use App\Services\Validator;
use Intervention\Image\Constraint;
use Intervention\Image\ImageManager;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * UploadController
 */
class UploadController extends Controller
{
    public function __construct(
        protected Session $session,
        protected Validator $validator,
        protected ImageManager $imageManager,
    ) {}

    /**
     * Upload file
     *
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function upload(Request $request, Response $response): Response
    {
        $user  = getUser();
        $input = (array) $request->getParsedBody();
        $files = $request->getUploadedFiles();
        $input = array_merge($input, $files);

        $id = $input['id'] ?? 0;

        if ($id) {
            $model = Story::query()->find($id);

            if (! $model) {
                return $this->json($response, [
                    'success' => false,
                    'message' => 'Запись не найдена!',
                ]);
            }

            $this->validator->custom(
                $model->user_id === $user->id || isAdmin(),
                'Вы не являетесь автором данной записи!'
            );
        } else {
            $model = new Story();
        }

        $this->validator
            ->required(['csrf', 'file'])
            ->same('csrf', $this->session->get('csrf'), 'Неверный идентификатор сессии, повторите действие!')
            ->file('file', [
                'size_max'   => setting('file.size_max'),
                'weight_max' => setting('image.weight_max'),
                'weight_min' => setting('image.weight_min'),
            ]);

        $countFiles = File::query()
            ->where('user_id', getUser('id'))
            ->where('story_id', $id)
            ->count();

        $this->validator->custom(
            $countFiles < setting('file.total_max'),
            sprintf('Разрешено загружать не более %d файлов!', setting('file.total_max'))
        );

        if ($this->validator->isValid($input)) {
            $file      = $input['file'];
            $filename  = sanitize($file->getClientFilename());
            $extension = getExtension($filename);
            $path      = $model->uploadPath . '/' . uniqueName($extension);

            $img = $this->imageManager->make($file->getFilePath());
            $img->resize(setting('image.resize'), setting('image.resize'), static function (Constraint $constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            $img->save(publicPath($path));

            $file = File::query()->insert([
                'user_id'    => $user->id,
                'story_id'   => $id,
                'path'       => $path,
                'name'       => $filename,
                'ext'        => $extension,
                'size'       => $file->getSize(),
                'created_at' => time(),
            ]);

            return $this->json($response, [
                'success' => true,
                'id'      => $file->id,
                'path'    => $path,
                'name'    => $filename,
                'type'    => 'image',
            ]);
        }

        return $this->json($response, [
            'success' => false,
            'message' => current($this->validator->getErrors()),
        ]);
    }

    /**
     * Delete file
     *
     * @param int      $id
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function destroy(int $id, Request $request, Response $response): Response
    {
        $user  = getUser();
        $input = (array) $request->getParsedBody();

        $file = File::query()->find($id);

        if (! $file) {
            return $this->json($response, [
                'success'  => false,
                'message'  => 'Файл не найден!'
            ]);
        }

        $this->validator
            ->required(['csrf'])
            ->same('csrf', $this->session->get('csrf'), 'Неверный идентификатор сессии, повторите действие!')
            ->custom($file->user_id === $user->id || isAdmin(), 'Вы не являетесь автором данного файла!');

        if ($this->validator->isValid($input)) {
            $file->delete();

            return $this->json($response, [
                'success' => true,
                'path'    => $file->path,
            ]);
        }

        return $this->json($response, [
            'success' => false,
            'message' => current($this->validator->getErrors()),
        ]);
    }
}
