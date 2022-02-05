<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\Paginator;
use App\Services\Setting;
use App\Services\View;
use Odan\Session\SessionInterface;
use Psr\Container\ContainerInterface;

/**
 * Controller
 */
class Controller
{
    protected View $view;
    protected Paginator $paginator;
    protected SessionInterface $session;
    protected Setting $settings;

    public function __construct(
        protected ContainerInterface $container
    ) {
        $this->view      = $container->get(View::class);
        $this->paginator = $container->get(Paginator::class);
        $this->settings  = $container->get(Setting::class);
        $this->session   = $container->get(SessionInterface::class);
    }
}
