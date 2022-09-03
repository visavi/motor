<?php

declare(strict_types=1);

namespace App\Models;

use MotorORM\Builder;

/**
 * Class Guestbook
 *
 * @property int $id
 * @property string $user_id
 * @property string $text
 * @property string $name
 * @property int $created_at
 *
 * @property-read User $user
 */
class Guestbook extends Model
{
    protected string $filePath = __DIR__ . '/../../database/guestbook.csv';

    /**
     * Возвращает связь пользователей
     *
     * @return Builder
     */
    public function user(): Builder
    {
        return $this->hasOne(User::class, 'user_id');
    }
}
