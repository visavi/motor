<?php

use App\Factories\AppFactory;

// Встроенный сервер php сам отдаёт файлы, которые лежат на диске.
// Apache делает это правилом !-f в .htaccess
if (PHP_SAPI === 'cli-server') {
    $file = __DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    if (is_file($file)) {
        return false;
    }
}

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::createInstance();
$app->run();
