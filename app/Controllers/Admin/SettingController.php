<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Models\Setting;
use App\Repositories\SettingRepository;
use App\Services\Session;
use App\Services\Validator;
use App\Services\View;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * SettingController
 */
class SettingController extends Controller
{
    public function __construct(
        protected View $view,
        protected Session $session,
        protected SettingRepository $settingRepository,
        protected Validator $validator,
    ) {}

    /**
     * Site settings
     *
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function index(Request $request, Response $response): Response
    {
        $query = $request->getQueryParams();

        $settings = $this->settingRepository->getSettings();
        $action = $query['action'] ?? 'app';

        return $this->view->render(
            $response,
            'admin/settings/index',
            compact('settings', 'action')
        );
    }

    /**
     * Store settings
     *
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function store(Request $request, Response $response): Response
    {
        $input = (array) $request->getParsedBody();
        $query = $request->getQueryParams();
        $action = $query['action'] ?? 'main';

        $this->validator
            ->required(['csrf', 'settings'])
            ->same('csrf', $this->session->get('csrf'), 'Неверный идентификатор сессии, повторите действие!');

        // Если поле пустое и не помеченное как необязательное
        foreach ($input['settings'][$action] as $name => $value) {
            if (! isset($input['optional'][$action][$name]) && $value === '') {
                $this->validator->addError($name, sprintf('Поле %s обязательное для заполнения', $name));
            }
        }

        if ($this->validator->isValid($input)) {
            foreach ($input['settings'][$action] as $name => $value) {
                if (isset($input['modifier'][$action][$name])) {
                    $value *= $input['modifier'][$action][$name];
                }

                Setting::query()->where('name', $action . '.' . $name)->update(['value' => $value]);
            }

            $this->session->set('flash', ['success' => 'Настройки успешно сохранены!']);
        } else {
            $this->session->set('flash', ['errors' => $this->validator->getErrors(), 'old' => $input]);
        }

        return $this->redirect($response, route('admin-settings', [], ['action' => $action]));
    }
}
