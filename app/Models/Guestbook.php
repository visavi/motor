<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Class Guestbook
 *
 * @property int $id
 * @property string $user_id
 * @property string $text
 * @property string $name
 * @property int $created_at
 */
class Guestbook extends Model
{
    protected string $filePath = __DIR__ . '/../../database/guestbook.csv';

    /**
     * Возвращает связь пользователей
     */
    public function user(): mixed
    {
        return $this->relation(User::class, 'user_id')->first();
    }
}
