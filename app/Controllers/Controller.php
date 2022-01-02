<?php

namespace App\Controllers;

use App\Paginator;
use Psr\Container\ContainerInterface;
use Slim\Views\Twig;

/**
 * Controller
 */
class Controller
{
    protected Twig $view;
    protected Paginator $paginator;

    public function __construct(
        protected ContainerInterface $container
    ) {
        $this->view = $container->get('view');
        $this->paginator = $container->get('paginator');
    }
}
