<?php

declare(strict_types=1);

use App\Entities\Story;
use App\Services\Setting;
use App\Services\View;
use DI\ContainerBuilder;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\ORMSetup;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Response;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\CacheInterface;

return static function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        // Set view in Container
        View::class => function() {
            return new View(dirname(__DIR__) . '/resources/views');
        },

        CacheInterface::class => function() {
            return new FilesystemAdapter('cache', 3600, dirname(__DIR__) . '/storage');
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

        EntityManagerInterface::class => function (): EntityManagerInterface {
            /*$cache = $settings['doctrine']['dev_mode'] ?
                DoctrineProvider::wrap(new ArrayAdapter()) :
                DoctrineProvider::wrap(new FilesystemAdapter(directory: $settings['doctrine']['cache_dir']));*/

            $config = ORMSetup::createAttributeMetadataConfiguration(
                paths: [__DIR__ . '/Entities'],
                isDevMode: true,
                /*cache: $cache,*/
            );

            $connection = DriverManager::getConnection([
                'dbname'   => 'motor',
                'user'     => 'root',
                'password' => 'root',
                'host'     => 'localhost',
                'driver'   => 'pdo_mysql',
                'charset'  => 'utf8mb4',
            ], $config);

           return new EntityManager($connection, $config);
        },

/*        ClassMetadata::class => function () {
            return new ClassMetadata('');
        },*/
    ]);
};
