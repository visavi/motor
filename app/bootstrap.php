<?php

use App\Factory\ContainerFactory;
use Slim\App;

// Build DI Container instance
$container = (new ContainerFactory())->createInstance();

// Create App instance
return $container->get(App::class);
