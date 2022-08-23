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
 * @property-read Story $post
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

    /**
     * Возвращает связь статьи
     *
     * return Builder
     */
    public function post(): Builder
    {
        return $this->HasOne(Story::class, 'post_id');
    }
}
