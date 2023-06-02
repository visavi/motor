<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\NotificationRepository;
use App\Services\Session;
use App\Services\Validator;
use App\Services\View;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * NotificationController
 */
class NotificationController extends Controller
{
    public function __construct(
        protected NotificationRepository $notificationRepository,
        protected Session $session,
        protected Validator $validator,
        protected View $view,
    ) {}

    /**
     * Notifications
     *
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function index(Request $request, Response $response): Response
    {
        $notifications = $this->notificationRepository->getNotifications();
        $this->notificationRepository->markAsRead();

        return $this->view->render(
            $response,
            'notifications/index',
            compact('notifications')
        );
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
        $notification = $this->notificationRepository->getUserNotificationById($id);
        if (! $notification) {
            abort(404, 'Уведомление не найдено!');
        }

        $input = (array) $request->getParsedBody();

        $this->validator
            ->required('csrf')
            ->same('csrf', $this->session->get('csrf'), 'Неверный идентификатор сессии, повторите действие!');

        if ($this->validator->isValid($input)) {
            $notification->delete();

            $this->session->set('flash', ['success' => 'Уведомление успешно удалено!']);
        } else {
            $this->session->set('flash', ['errors' => $this->validator->getErrors()]);
        }

        return $this->redirect($response, route('notifications'));
    }
}
