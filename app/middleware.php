<?php
declare(strict_types=1);

use Slim\App;
use Slim\Middleware\Session;

return function (App $app) {
    $app->add(
        new Session([
            'name'        => 'motor_session',
            'lifetime'    => '1 hour',
            'httponly'    => true,
            'autorefresh' => true,
        ])
    );
};
