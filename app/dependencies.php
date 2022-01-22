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
        'view' => function(ContainerInterface $container) {
            $twig = Twig::create(__DIR__ . '/../resources/views', [
                'cache'       => false/* __DIR__ . '/../var/views'*/,
                'auto_reload' => true,
                'debug'       => true,
            ]);

            $filter = new \Twig\TwigFilter('bbCode', 'bbCode', ['is_safe' => ['html']]);
            $twig->getEnvironment()->addFilter($filter);

            // Old
            $session = $container->get('session');
            $function = new \Twig\TwigFunction('old', function (string $key, mixed $default = null) use ($session) {
                if (! $session['flash']['old']) {
                    return $default;
                }

                return $session['flash']['old'][$key] ?? $default;
            });
            $twig->getEnvironment()->addFunction($function);

            // HasError
            $function = new \Twig\TwigFunction('hasError', function (string $field) use ($session) {
                if ($session['flash']['errors']) {
                    return $session['flash']['errors'][$field] ? ' is-invalid' : ' is-valid';
                }

                return '';
            });
            $twig->getEnvironment()->addFunction($function);

            // Get Error
            $function = new \Twig\TwigFunction('getError', fn (string $field) => $session['flash']['errors'][$field] ?? null);
            $twig->getEnvironment()->addFunction($function);

            $twig->getEnvironment()->addGlobal('session', $container->get('session'));
            $twig->addExtension(new \Twig\Extension\DebugExtension());

            return $twig;
        },

        // Set pagination in Container
        'paginator' => function(ContainerInterface $container) {
            return new Paginator($container->get('view'));
        },

        // Set session in Container
        'session' => function () {
            return new \SlimSession\Helper();
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
