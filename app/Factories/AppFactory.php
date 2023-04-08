<?php

declare(strict_types=1);

namespace App\Factories;

use DI\Bridge\Slim\Bridge;
use DI\Container;
use Slim\App;

/**
 * App Factory.
 */
class AppFactory extends Container
{
    protected static ?App $instance = null;

    /**
     * Create the shared instance of the container.
     *
     * @return App
     */
    public static function createInstance(): App
    {
        // Instantiate PHP-DI ContainerBuilder
        $container = require __DIR__ . '/../container.php';

        // Instantiate App
        $app = Bridge::create($container);

        // Register middleware
        $middleware = require __DIR__ . '/../middleware.php';
        $middleware($app);

        // Register routes
        $routes = require __DIR__ . '/../routes.php';
        $routes($app);

        return static::$instance = $app;
    }

    /**
     * Get the globally available instance of the container
     *
     * @return App
     */
    public static function getInstance(): App
    {
        if (static::$instance === null) {
            static::$instance = static::createInstance();
        }

        return static::$instance;
    }
}
