<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\Str;
use MotorORM\Builder;
use MotorORM\Collection;

/**
 * Class Guestbook
 *
 * @property int $id
 * @property string $user_id
 * @property string $story_id
 * @property string $text
 * @property int $rating
 * @property int $created_at
 *
 * @property-read User $user
 * @property-read Story $story
 * @property-read Poll $poll
 * @property-read Collection<Poll> $polls
 */
class Comment extends Model
{
    protected string $filePath = __DIR__ . '/../../storage/database/comments.csv';

    /**
     * The attributes that should be cast.
     */
    protected array $casts = [
        'rating' => 'int',
    ];

    /**
     * Возвращает связь пользователя
     *
     * return Builder
     */
    public function user(): Builder
    {
        return $this->HasOne(User::class, 'id', 'user_id');
    }

    /**
     * Возвращает связь статьи
     *
     * return Builder
     */
    public function story(): Builder
    {
        return $this->HasOne(Story::class, 'id', 'story_id');
    }

    /**
     * Возвращает связь голосования пользователя
     *
     * @return Builder
     */
    public function poll(): Builder
    {
        return $this->hasOne(Poll::class, 'entity_id')
            ->where('user_id', getUser('id'))
            ->where('entity_name', 'comment');
    }

    /**
     * Возвращает связь голосований
     *
     * @return Builder
     */
    public function polls(): Builder
    {
        return $this->hasMany(Poll::class, 'entity_id')
            ->where('entity_name', 'comment');
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

    /**
     * Get format rating
     *
     * @return string Форматированное число
     */
    public function getRating(): string
    {
        if ($this->rating > 0) {
            $rating = '<span style="color:#00aa00">+' . $this->rating . '</span>';
        } elseif ($this->rating < 0) {
            $rating = '<span style="color:#ff0000">' . $this->rating . '</span>';
        } else {
            $rating = '<span>0</span>';
        }

        return $rating;
    }

    /**
     * Delete comment
     *
     * @return int
     */
    public function delete(): int
    {
        // delete polls
        foreach ($this->polls as $poll) {
            $poll->delete();
        }

        return parent::delete();
    }
}
