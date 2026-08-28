<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\BBCode;
use App\Services\Str;
use MotorORM\Collection;
use MotorORM\Query;
use MotorORM\Relation;

/**
 * Class Comment
 *
 * @property int $id
 * @property int $user_id
 * @property int $story_id
 * @property string $text
 * @property int $rating
 * @property int $created_at
 * @property int $parent_id
 *
 * @property-read User $user
 * @property-read Story $story
 * @property-read Poll $poll
 * @property-read Collection<Poll> $polls
 */
class Comment extends Model
{
    /**
     * Table name
     */
    protected string $table = 'comments.csv';

    /**
     * The attributes that should be cast.
     */
    protected array $casts = [
        'rating'     => 'int',
        'user_id'    => 'int',
        'story_id'   => 'int',
        'parent_id'  => 'int',
        'created_at' => 'int',
    ];

    /**
     * Возвращает связь пользователя
     *
     * @return Relation
     */
    public function user(): Relation
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    /**
     * Возвращает связь статьи
     *
     * @return Relation
     */
    public function story(): Relation
    {
        return $this->hasOne(Story::class, 'id', 'story_id');
    }

    /**
     * Возвращает связь голосования пользователя
     *
     * @return Relation
     */
    public function poll(): Relation
    {
        return $this->hasOne(Poll::class, 'entity_id')->constrain(
            static fn (Query $query) => $query
                ->where('user_id', getUser('id'))
                ->where('entity_name', 'comment')
        );
    }

    /**
     * Возвращает связь голосований
     *
     * @return Relation
     */
    public function polls(): Relation
    {
        return $this->hasMany(Poll::class, 'entity_id')->constrain(
            static fn (Query $query) => $query->where('entity_name', 'comment')
        );
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
        $bbCode = new BBCode();
        if (Str::wordCount($this->text) > $words) {
            return $bbCode->truncate($this->text, $words);
        }

        return $bbCode->handle($this->text);
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

    /**
     * Get text
     *
     * @return string
     */
    public function getText(): string
    {
        return (new BBCode())->handle($this->text);
    }
}
