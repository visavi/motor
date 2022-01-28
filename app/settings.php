<?php
declare(strict_types=1);

use DI\ContainerBuilder;

return function (ContainerBuilder $containerBuilder) {

    // Global Settings Object
    $containerBuilder->addDefinitions(['setting' => [
        'debug'     => false,
        'guestbook' => [
            'per_page' => 10,
        ]
    ]]);
};
