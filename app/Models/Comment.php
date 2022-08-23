<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\Str;
use MotorORM\Builder;

/**
 * Class Guestbook
 *
 * @property int $id
 * @property string $user_id
 * @property string $story_id
 * @property string $text
 * @property int $created_at
 *
 * @property-read User $user
 * @property-read Story $story
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
    public function story(): Builder
    {
        return $this->HasOne(Story::class, 'story_id');
    }

    /**
     * Возвращает сокращенный текст комментария
     *
     * @param int $words
     *
     * @return string
     */
    public function shortText(int $words = 30): string
    {
        if (Str::wordCount($this->text) > $words) {
            return bbCodeTruncate($this->text, $words);
        }

        return bbCode($this->text);
    }
}
