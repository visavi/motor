<?php

declare(strict_types=1);

use App\Models\User;
use App\Services\Setting;
use DI\ContainerBuilder;

return function (ContainerBuilder $containerBuilder) {
    // Global Settings Object
    $containerBuilder->addDefinitions([
        Setting::class => function () {
            return new Setting([
                'debug' => true,

                'main' => [
                    'title'       => 'Добро пожаловать',
                    'guest_name'  => 'Гость',
                    'delete_name' => 'Удаленный',
                ],

                'story' => [
                    'per_page'         => 10,
                    'title_min_length' => 5,
                    'title_max_length' => 50,
                    'text_min_length'  => 5,
                    'text_max_length'  => 5000,
                    'short_words'      => 100, // Количество слов в сокращенной статье
                    'tags_min_length'  => 2,
                    'tags_max_length'  => 50,
                ],

                'comment' => [
                    'text_min_length'  => 5,
                    'text_max_length'  => 1000,
                ],

                'guestbook' => [
                    'per_page'         => 10,
                    'text_min_length'  => 5,
                    'text_max_length'  => 1000,
                    'name_min_length'  => 3,
                    'name_max_length'  => 20,
                    'allow_guests'     => true, // Разрешить гостям писать
                ],

                'session' => [
                    'cookie_name'     => 'motor_session',
                    'cookie_domain'   => false,
                    'cookie_secure'   => true,
                    'cookie_httponly' => true,
                    'cookie_samesite' => 'Lax',
                ],

                'file' => [
                    'size_max'  => 1024 * 1000 * 5, // Максимальный вес 5MB
                    'total_max' => 5, // Максимальное количество загружаемых файлов
                ],

                'image' => [
                    'resize'     => 1000, // Обрезать изображения px
                    'weight_max' => null, // Максимальный размер px
                    'weight_min' => 100,  // Минимальный размер px
                ],

                'roles' => [
                    User::BOSS   => 'Босс',
                    User::ADMIN  => 'Админ',
                    User::MODER  => 'Модератор',
                    User::EDITOR => 'Редактор',
                    User::USER   => 'Пользователь',
                    User::PENDED => 'Ожидающий',
                    User::BANNED => 'Забаненный',
                ],

                'displayErrorDetails' => true, // Should be set to false in production
                'logError'            => false,
                'logErrorDetails'     => false,
                'logger' => [
                    'name' => 'motor-app',
                    'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
                    //'level' => Logger::DEBUG,
                ],
            ]);
        }
    ]);
};
