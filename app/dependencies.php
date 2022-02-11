<?php

declare(strict_types=1);

use App\Middleware\UserAuthMiddleware;
use App\Services\Session;
use App\Services\Setting;
use App\Services\View;
use DI\ContainerBuilder;
use League\Plates\Engine;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Log\LoggerInterface;
use Slim\App;
use Slim\Factory\AppFactory;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        // Set view in Container
        View::class => function(ContainerInterface $container) {
            $view = new View(dirname(__DIR__) . '/resources/views');
            //$view->getEngine()->addData(['session' => $container->get(SessionInterface::class)]);

            return $view;
        },

        /*Session::class => function (ContainerInterface $container) {
            $session = $container->get(Session::class);

            return $session;
        }*/

/*        Engine::class => function(ContainerInterface $container) {
            $view = new Engine(dirname(__DIR__) . '/resources/views');
            $view->addData(['session' => $container->get(SessionInterface::class)]);

            return $view;
        },*/

        /*SessionInterface::class => function (ContainerInterface $container) {
            $settings = $container->get(Setting::class);
            $session = new PhpSession();
            $session->setOptions($settings->get('session'));

            return $session;
        },*/

        /*SessionMiddleware::class => function (ContainerInterface $container) {
            return new SessionMiddleware($container->get(SessionInterface::class));
        },*/

        /*ResponseFactoryInterface::class => function (ContainerInterface $container) {
            return $container->get(App::class)->getResponseFactory();
        },

        App::class => function (ContainerInterface $container) {
            AppFactory::setContainer($container);

            return AppFactory::create();
        },*/

        /*LoggerInterface::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);

            $loggerSettings = $settings->get('logger');
            $logger = new Logger($loggerSettings['name']);

            $processor = new UidProcessor();
            $logger->pushProcessor($processor);

            $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
            $logger->pushHandler($handler);

            return $logger;
        },*/
    ]);
};
