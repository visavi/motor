<?php

use App\Factories\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::createInstance();
$app->run();
