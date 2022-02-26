<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Class Guestbook
 *
 * @property int $id
 * @property string $login
 * @property string $title
 * @property string $text
 * @property string $image
 * @property int $created_at
 */
class Guestbook extends Model
{
    protected string $filePath = __DIR__ . '/../../database/guestbook.csv';


    /**
     * Возвращает связь пользователей
     */
/*    public function user(): User
    {
        return $this->relate(User::class, 'user_id');
    }*/
}
