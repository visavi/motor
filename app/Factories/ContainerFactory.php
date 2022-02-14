<?php

declare(strict_types=1);

namespace App\Factories;

use DI\Container;
use Psr\Container\ContainerInterface;

/**
 * Container Factory.
 */
class ContainerFactory extends Container
{
    protected static ?ContainerInterface $instance = null;

    /**
     * Create the shared instance of the container.
     *
     * @return ContainerInterface
     */
    public static function createInstance(): ContainerInterface
    {
        $container = require __DIR__ . '/../../app/container.php';

        return static::$instance = $container;
    }

    /**
     * Get the globally available instance of the container
     *
     * @return ContainerInterface
     */
    public static function getInstance(): ContainerInterface
    {
        if (static::$instance === null) {
            static::$instance = static::createInstance();
        }

        return static::$instance;
    }

    /**
     * Get container.
     *
     * @return ContainerInterface|Container The container
     */
    /*public function createInstance(): ContainerInterface
    {
        $containerBuilder = new ContainerBuilder();

        // Set up settings
        $containerBuilder->addDefinitions(__DIR__ . '/../../config/container.php');

        // Build PHP-DI Container instance
        return $containerBuilder->build();
    }*/
}
