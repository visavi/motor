<?php
declare(strict_types=1);

//use App\Application\Settings\SettingsInterface;
use App\Services\Paginator;
use DI\ContainerBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Slim\Views\Twig;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([

        // Set view in Container
        'view' => function() {
            $twig = Twig::create(__DIR__ . '/../resources/views', [
                'cache'       => false/* __DIR__ . '/../var/views'*/,
                'auto_reload' => true,
                'debug'       => true,
            ]);

            $filter = new \Twig\TwigFilter('bbCode', 'bbCode', ['is_safe' => ['html']]);

            $twig->getEnvironment()->addFilter($filter);
            $twig->addExtension(new \Twig\Extension\DebugExtension());

            return $twig;
        },

        // Set pagination in Container
        'paginator' => function(ContainerInterface $container) {
            return new Paginator($container->get('view'));
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
