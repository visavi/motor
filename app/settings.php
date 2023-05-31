<?php

declare(strict_types=1);

use App\Models\User;
use App\Repositories\SettingRepository;
use App\Services\Setting;
use DI\ContainerBuilder;
use Monolog\Logger;

return static function (ContainerBuilder $containerBuilder)
{
    // Global Settings Object
    $containerBuilder->addDefinitions([
        Setting::class => function () {
            $settings = (new SettingRepository())->getSettings();

            return new Setting([
                'app' => [
                    'name' => $settings['app.name'], // Название сайта
                    'url'  => $settings['app.url'],  // Адрес сайта
                ],

                'main' => [
                    'title'          => $settings['main.title'],          // Заголовок сайта
                    'allow_register' => $settings['main.allow_register'], // Разрешить регистрацию
                    'confirm_email'  => $settings['main.confirm_email'],  // Подтверждать email
                    'guest_name'     => $settings['main.guest_name'],     // Имя гостя
                    'delete_name'    => $settings['main.delete_name'],    // Имя удаленного пользователя
                ],

                'mailer' => [
                    'dsn'        => $settings['mailer.dsn'],        // Параметры smtp (smtp://localhost:1025)
                    'from_email' => $settings['mailer.from_email'], // Email отправителя
                    'from_name'  => $settings['mailer.from_name'],  // Имя отправителя
                ],

                'story' => [
                    'active'           => $settings['story.active'],           // Публиковать посты без модерации
                    'allow_posting'    => $settings['story.allow_posting'],    // Разрешать пользователям публиковать статьи
                    'per_page'         => $settings['story.per_page'],         // Количество статей на страницу
                    'title_min_length' => $settings['story.title_min_length'], // Минимальная длина заголовка
                    'title_max_length' => $settings['story.title_max_length'], // Максимальная длина заголовка
                    'text_min_length'  => $settings['story.text_min_length'],  // Минимальная длина статьи
                    'text_max_length'  => $settings['story.text_max_length'],  // Максимальная длина статьи
                    'short_words'      => $settings['story.short_words'],      // Количество слов в сокращенной статье
                    'tags_max'         => $settings['story.tags_max'],         // Максимальное количество тегов
                    'tags_min_length'  => $settings['story.tags_min_length'],  // Минимальное количество символов в теге
                    'tags_max_length'  => $settings['story.tags_max_length'],  // Максимальное количество символов в теге
                ],

                'comment' => [
                    'text_min_length'  => $settings['comment.text_min_length'],
                    'text_max_length'  => $settings['comment.text_max_length'],
                ],

                'guestbook' => [
                    'allow_guests'     => $settings['guestbook.allow_guests'],   // Разрешить гостям писать сообщения
                    'per_page'         => $settings['guestbook.per_page'],
                    'text_min_length'  => $settings['guestbook.text_min_length'],
                    'text_max_length'  => $settings['guestbook.text_max_length'],
                    'name_min_length'  => $settings['guestbook.name_min_length'],
                    'name_max_length'  => $settings['guestbook.name_max_length'],
                ],

                'session' => [
                    'cookie_name'     => 'motor_session',
                    'cookie_domain'   => false,
                    'cookie_secure'   => isset($_SERVER['HTTPS']),
                    'cookie_httponly' => true,
                    'cookie_samesite' => 'Lax',
                ],

                'file' => [
                    'size_max'   => $settings['file.size_max'],  // Максимальный вес
                    'total_max'  => $settings['file.total_max'], // Максимальное количество загружаемых файлов
                    'extensions' => explode(',', $settings['file.extensions']),
                ],

                'image' => [
                    'resize'     => $settings['image.resize'],     // Обрезать изображения px
                    'weight_max' => $settings['image.weight_max'], // Максимальный размер px
                    'weight_min' => $settings['image.weight_min'], // Минимальный размер px
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

                'user' => [
                    'per_page' => $settings['user.per_page'],
                ],

                'captcha' => [
                    'length'  => $settings['captcha.length'], // Количество символов
                    'symbols' => $settings['captcha.symbols'], // Список допустимых символов
                ],

                'displayErrorDetails' => false, // Should be set to false in production
                'logError'            => true,
                'logErrorDetails'     => true,
                'logger' => [
                    'name'     => 'motor_app',
                    'path'     => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../storage/logs/motor.log',
                    'level'    => Logger::DEBUG,
                    'maxFiles' => 7,
                ],
            ]);
        }
    ]);
};
