<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\BBCode;
use App\Services\Str;
use App\Services\View;
use MotorORM\Collection;
use MotorORM\Query;
use MotorORM\Relation;

/**
 * Class Story
 *
 * @property int $id
 * @property int $user_id
 * @property string $slug
 * @property bool $active
 * @property string $title
 * @property string $text
 * @property int $rating
 * @property int $reads
 * @property bool $locked
 * @property int $created_at
 *
 * @property-read User $user
 * @property-read Poll $poll
 * @property-read Favorite $favorite
 * @property-read Collection<Tag> $tags
 * @property-read Collection<File> $files
 * @property-read Collection<Comment> $comments
 * @property-read Collection<Read> $storyReads
 * @property-read Collection<Poll> $polls
 * @property-read Collection<Favorite> $favorites
 * @method $this active()
 */
class Story extends Model
{
    /**
     * Table name
     */
    protected string $table = 'stories.csv';

    /**
     * The attributes that should be cast.
     */
    protected array $casts = [
        'active'     => 'bool',
        'rating'     => 'int',
        'reads'      => 'int',
        'locked'     => 'bool',
        'user_id'    => 'int',
        'created_at' => 'int',
    ];

    /**
     * Директория загрузки файлов
     */
    public string $uploadPath = '/uploads/stories';

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
     * Возвращает связь голосования пользователя
     *
     * @return Relation
     */
    public function poll(): Relation
    {
        return $this->hasOne(Poll::class, 'entity_id')->constrain(
            static fn (Query $query) => $query
                ->where('user_id', getUser('id'))
                ->where('entity_name', 'story')
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
            static fn (Query $query) => $query->where('entity_name', 'story')
        );
    }

    /**
     * Возвращает связь просмотров
     *
     * @return Relation
     */
    public function storyReads(): Relation
    {
        return $this->hasMany(Read::class);
    }

    /**
     * Возвращает связь файлов
     *
     * @return Relation
     */
    public function files(): Relation
    {
        return $this->hasMany(File::class);
    }

    /**
     * Возвращает связь комментариев
     *
     * @return Relation
     */
    public function comments(): Relation
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Возвращает связь избранного пользователя
     *
     * @return Relation
     */
    public function favorite(): Relation
    {
        return $this->hasOne(Favorite::class, 'story_id')->constrain(
            static fn (Query $query) => $query->where('user_id', getUser('id'))
        );
    }

    /**
     * Возвращает связь с избранным
     *
     * @return Relation
     */
    public function favorites(): Relation
    {
        return $this->hasMany(Favorite::class);
    }

    /**
     * Возвращает связь с тегами
     *
     * @return Relation
     */
    public function tags(): Relation
    {
        return $this->hasMany(Tag::class);
    }

    /**
     * Scope active
     *
     * @param Query $query
     *
     * @return Query
     */
    public function scopeActive(Query $query): Query
    {
        return $query->where('active', true);
    }
    /**
     * Get comments tree
     *
     * @param int $parentId
     * @param int $depth
     * @param int $maxDepth
     *
     * @return array
     */
    public function commentsTree(int $parentId = 0, int $depth = 0, int $maxDepth = 5): array
    {
        $tree = [];

        foreach ($this->comments as $comment) {
            $parentNotExists = $parentId === 0
                && ! $this->comments->contains(function (Comment $value) use ($comment) {
                    return $value->id === $comment->parent_id;
                });

            if ($comment->parent_id === $parentId || $parentNotExists) {
                $comment->depth = $depth;

                $comment->child = $this->commentsTree($comment->id, $depth < $maxDepth ? $depth + 1 : $depth, $maxDepth);

                $tree[] = $comment;
            }
        }

        return $tree;
    }

    /**
     * Delete story
     *
     * @return int
     */
    public function delete(): int
    {
        // delete files
        foreach ($this->files as $file) {
            $file->delete();
        }

        // delete comments
        foreach ($this->comments as $comment) {
            $comment->delete();
        }

        // delete reads
        foreach ($this->storyReads as $read) {
            $read->delete();
        }

        // delete polls
        foreach ($this->polls as $poll) {
            $poll->delete();
        }

        // delete favorites
        foreach ($this->favorites as $favorite) {
            $favorite->delete();
        }

        // delete tags
        foreach ($this->tags as $tag) {
            $tag->delete();
        }

        return parent::delete();
    }

    /**
     * Возвращает сокращенный текст статьи
     *
     * @param int $words
     *
     * @return string
     */
    public function shortText(int $words = 100): string
    {
        $bbCode = new BBCode();
        $more = app(View::class)->fetch('app/_more', ['link' => $this->getLink()]);

        if (str_contains($this->text, '[cut]')) {
            return $bbCode->handle(current(explode('[cut]', $this->text, 2))) . $more;
        }

        if (Str::wordCount($this->text) > $words) {
            return $bbCode->truncate($this->text, $words) . $more;
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
     * Get tags
     *
     * @return string
     */
    public function getTags(): string
    {
        $tagList = [];
        foreach ($this->tags as $tag) {
            $tagList[] = '<a href="/tags/' . urlencode(escape($tag->tag)) . '">' . escape($tag->tag) . '</a>';
        }

        return implode(', ', $tagList);
    }

    /**
     * Get link
     *
     * @return string
     */
    public function getLink(): string
    {
        return route('story-view', ['slug' => sprintf('%s-%d', $this->slug, $this->id)]);
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
