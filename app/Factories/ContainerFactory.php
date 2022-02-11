<?php

declare(strict_types=1);

namespace App\Factories;

use DI\Container;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;

/**
 * Container Factory.
 */
final class ContainerFactory
{
    /**
     * Get container.
     *
     * @return ContainerInterface|Container The container
     */
    public function createInstance(): ContainerInterface
    {
        $containerBuilder = new ContainerBuilder();

        // Set up settings
        $containerBuilder->addDefinitions(__DIR__ . '/../../config/container.php');

        // Build PHP-DI Container instance
        return $containerBuilder->build();
    }
}
