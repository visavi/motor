<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Repositories\NotificationRepository;

/**
 * NotificationService class
 */
class NotificationService
{
    /**
     * @param string $text
     * @param string $url
     * @param string $title
     *
     * @return void
     */
    public function sendNotify(string $text, string $url, string $title): void
    {
        /*$parseText = preg_replace('|\[quote(.*?)\](.*?)\[/quote\]|s', '', $text);*/
        preg_match_all('/(?<=^|\s|=)@([\w\-]+)/', $text, $matches);

        if (! empty($matches[1])) {
            $login = getUser('login') ?? setting('main.guest_name');
            $usersAnswer = array_unique(array_diff($matches[1], [$login]));

            foreach ($usersAnswer as $user) {
                $user = User::query()->where('login', $user)->first();

                if ($user) {
                    $page = sprintf('[b][url=%s]%s[/url][/b]', $url, $title);
                    $message = sprintf('Пользователь @%s упомянул вас на странице %s%sТекст сообщения: %s', $login, $page, PHP_EOL, $text);

                    (new NotificationRepository())->createNotification($user, $message);
                }
            }
        }
    }
}
