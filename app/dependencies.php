<?php

declare(strict_types=1);

use App\Services\Setting;
use App\Services\View;
use DI\ContainerBuilder;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Shieldon\SimpleCache\Cache;
use Slim\Psr7\Response;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        // Set view in Container
        View::class => function() {
            return new View(dirname(__DIR__) . '/resources/views');
        },

        Cache::class => function() {
            return new Cache('file', ['storage' => dirname(__DIR__) . '/storage/cache']);
        },

        ResponseInterface::class => function () {
            return new Response();
        },

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
