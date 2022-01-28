<?php
declare(strict_types=1);

use App\Services\Paginator;
use App\Services\View;
use DI\ContainerBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use SlimSession\Helper;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([

        // Set view in Container
        'view' => function() {
            //return new League\Plates\Engine(dirname(__DIR__) . '/resources/views');
            return new View(dirname(__DIR__) . '/resources/views');
        },

        // Set pagination in Container
        'paginator' => function(ContainerInterface $container) {
            return new Paginator($container->get('view'));
        },

        // Set session in Container
        'session' => function () {
            return new Helper();
        },


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
