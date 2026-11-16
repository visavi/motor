<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Exceptions\MigrationException;
use App\Services\BackupService;
use App\Services\MigrationService;
use App\Services\Session;
use App\Services\UpdateService;
use App\Services\Validator;
use App\Services\View;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use RuntimeException;

/**
 * MigrationController
 */
class MigrationController extends Controller
{
    public function __construct(
        protected Session $session,
        protected Validator $validator,
        protected View $view,
        protected MigrationService $migrationService,
        protected BackupService $backupService,
        protected UpdateService $updateService,
        protected LoggerInterface $logger,
    ) {}

    /**
     * Migrations
     *
     * @param Response $response
     *
     * @return Response
     */
    public function index(Response $response): Response
    {
        return $this->view->render(
            $response,
            'admin/migrations/index',
            [
                'applied' => $this->migrationService->applied(),
                'pending' => $this->migrationService->pending(),
                'failure' => $this->updateService->failure(),
            ]
        );
    }

    /**
     * Run pending migrations
     *
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function run(Request $request, Response $response): Response
    {
        $input = (array) $request->getParsedBody();

        $this->validator
            ->required('csrf')
            ->same('csrf', $this->session->get('csrf'), 'Неверный идентификатор сессии, повторите действие!');

        if (! $this->validator->isValid($input)) {
            $this->session->set('flash', ['errors' => $this->validator->getErrors()]);

            return $this->redirect($response, route('admin-migrations'));
        }

        if (! $this->migrationService->pending()) {
            $this->updateService->clearFailure();
            $this->session->set('flash', ['success' => 'Невыполненных миграций нет']);

            return $this->redirect($response, route('admin-migrations'));
        }

        $backup = null;

        try {
            $backup   = $this->backupService->create();
            $migrated = $this->migrationService->migrate();
            $this->updateService->clearFailure();

            $this->session->set('flash', [
                'success' => sprintf('Выполнено миграций: %d. Бэкап перед обновлением: %s', count($migrated), $backup),
            ]);
        } catch (MigrationException $e) {
            $this->logger->error('Миграция не выполнена', ['exception' => $e, 'backup' => $backup]);

            $this->session->set('flash', [
                'danger' => sprintf(
                    'Миграция %s не выполнена: %s. Бэкап перед обновлением: %s',
                    $e->migration,
                    $e->getPrevious()?->getMessage(),
                    $backup
                ),
            ]);
        } catch (RuntimeException $e) {
            $this->logger->error('Бэкап перед миграциями не создан', ['exception' => $e]);

            $this->session->set('flash', ['danger' => 'Бэкап не создан, миграции не запускались: ' . $e->getMessage()]);
        }

        return $this->redirect($response, route('admin-migrations'));
    }
}
