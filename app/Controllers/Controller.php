<?php

namespace App\Controllers;

use App\Services\Paginator;
use Psr\Container\ContainerInterface;
use Slim\Views\Twig;
use SlimSession\Helper as Session;

/**
 * Controller
 */
class Controller
{
    protected Twig $view;
    protected Paginator $paginator;
    protected Session $session;

    public function __construct(
        protected ContainerInterface $container
    ) {
        $this->view = $container->get('view');
        $this->paginator = $container->get('paginator');
        $this->session = $container->get('session');
    }
}
