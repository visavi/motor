<?php

use App\Models\User;
use MotorORM\Migration;

error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

require __DIR__ . '/../../vendor/autoload.php';

// Добавляет картинку и аватар в users
$userHeaders = User::query()->headers();

if (! in_array('picture', $userHeaders, true)) {
    $migration = new Migration(new User());

    try {
        $migration->column('picture')->after('name')->create();
        $migration->column('avatar')->after('picture')->create();
    } catch (Exception $exception) {
        echo $exception->getMessage();
    }
}

echo 'Все миграции выполнены успешно!';
