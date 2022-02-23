<?php

declare(strict_types=1);

use App\Services\View;
use DI\ContainerBuilder;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        // Set view in Container
        View::class => function() {
            return new View(dirname(__DIR__) . '/resources/views');
        },

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
