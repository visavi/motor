<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Services\LogReaderService;
use App\Services\Session;
use App\Services\Validator;
use App\Services\View;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * LogController
 */
class LogController extends Controller
{
    public function __construct(
        protected Session $session,
        protected Validator $validator,
        protected View $view,
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
        $logs = glob(storagePath('logs/*.log'));
        rsort($logs);

        if (! $logs) {
            abort(200, 'Логов еще нет!');
        }

        $files = [];
        foreach ($logs as $log) {
            $files[basename($log)] = $log;
        }

        $query = $request->getQueryParams();
        $currentLog = escape($query['log'] ?? '');

        $log = $files[$currentLog] ?? $logs[0];
        $reader = new LogReaderService($log);

        return $this->view->render(
            $response,
            'admin/logs/index',
            compact('logs', 'reader', 'currentLog')
        );
    }
}
