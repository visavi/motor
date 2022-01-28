<?php

namespace App\Controllers;

use App\Services\Paginator;
use App\Services\View;
use Psr\Container\ContainerInterface;
use SlimSession\Helper as Session;

/**
 * Controller
 */
class Controller
{
    protected View $view;
    protected Paginator $paginator;
    protected Session $session;
    protected array $setting;

    public function __construct(
        protected ContainerInterface $container
    ) {
        $this->view      = $container->get('view');
        $this->paginator = $container->get('paginator');
        $this->session   = $container->get('session');
        $this->setting   = $container->get('setting');
    }
}
