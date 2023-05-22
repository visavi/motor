<?php

declare(strict_types=1);

use App\Services\Setting;
use App\Services\View;
use DI\ContainerBuilder;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Shieldon\SimpleCache\Cache;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        // Set view in Container
        View::class => function() {
            return new View(dirname(__DIR__) . '/resources/views');
        },

        Cache::class => function() {
            return new Cache('file', ['storage' => dirname(__DIR__) . '/storage/cache']);
        },

        /*ResponseFactoryInterface::class => function (ContainerInterface $container) {
            return $container->get(App::class)->getResponseFactory();
        },

        App::class => function (ContainerInterface $container) {
            AppFactory::setContainer($container);

            return AppFactory::create();
        },*/

        LoggerInterface::class => function (ContainerInterface $container) {
            $setting = $container->get(Setting::class);

            $logger = new Logger($setting->get('logger.name'));
            $streamHandler = new RotatingFileHandler(
                $setting->get('logger.path'),
                $setting->get('logger.maxFiles'),
                $setting->get('logger.level'));
            $logger->pushHandler($streamHandler);

            return $logger;
        },
    ]);
};
