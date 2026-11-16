<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Services\BackupService;
use App\Services\Session;
use App\Services\Validator;
use App\Services\View;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use ZipArchive;

/**
 * BackupController
 */
class BackupController extends Controller
{
    public function __construct(
        protected Session $session,
        protected Validator $validator,
        protected View $view,
        protected BackupService $backupService,
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
        $files = glob(storagePath('backups/*.zip'));
        arsort($files);

        return $this->view->render(
            $response,
            'admin/backups/index',
            compact('files')
        );
    }

    /**
     * Create
     *
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function create(Request $request, Response $response): Response
    {
        $input = (array) $request->getParsedBody();

        $this->validator
            ->required('csrf')
            ->same('csrf', $this->session->get('csrf'), 'Неверный идентификатор сессии, повторите действие!');

        if ($this->validator->isValid($input)) {
            $this->backupService->create();

            $this->session->set('flash', ['success' => 'Бэкап успешно создан!']);
        } else {
            $this->session->set('flash', ['errors' => $this->validator->getErrors()]);
        }

        return $this->redirect($response, route('admin-backups'));
    }

    /**
     * Admin
     *
     * @param string   $name
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function view(string $name, Request $request, Response $response): Response
    {
        $filePath  = storagePath('backups/' . $name);

        if (! file_exists($filePath)) {
            abort(404, 'Бэкап не найден!');
        }

        $zip = new ZipArchive();
        if ($zip->open($filePath) !== true) {
            abort(200, 'Не удалось открыть архив!');
        }

        $files = [];
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $files[] = $zip->statIndex($i);
        }

        $countFiles = $zip->count();
        $zip->close();

        return $this->view->render(
            $response,
            'admin/backups/view',
            compact('name', 'files', 'countFiles')
        );
    }

    /**
     * Destroy
     *
     * @param string   $name
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function destroy(string $name, Request $request, Response $response): Response
    {
        $filePath  = storagePath('backups/' . $name);

        if (! file_exists($filePath)) {
            abort(404, 'Бэкап не найден!');
        }

        $input = (array) $request->getParsedBody();

        $this->validator
            ->required('csrf')
            ->same('csrf', $this->session->get('csrf'), 'Неверный идентификатор сессии, повторите действие!');

        if ($this->validator->isValid($input)) {
            unlink($filePath);

            $this->session->set('flash', ['success' => 'Бэкап успешно удален!']);
        } else {
            $this->session->set('flash', ['errors' => $this->validator->getErrors()]);
        }

        return $this->redirect($response, route('admin-backups'));
    }
}
