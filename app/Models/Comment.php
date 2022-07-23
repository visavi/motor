<?php

declare(strict_types=1);

namespace App\Models;

use MotorORM\Builder;

/**
 * Class Guestbook
 *
 * @property int $id
 * @property string $user_id
 * @property string $post_id
 * @property string $text
 * @property int $created_at
 *
 * @property-read User $user
 */
class Comment extends Model
{
    protected string $filePath = __DIR__ . '/../../database/comments.csv';

    /**
     * Возвращает связь пользователя
     *
     * return Builder
     */
    public function user(): Builder
    {
        return $this->HasOne(User::class, 'user_id');
    }
}
