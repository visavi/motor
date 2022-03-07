<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\Str;
use App\Services\View;

/**
 * Class Story
 *
 * @property int $id
 * @property string $user_id
 * @property string $slug
 * @property string $title
 * @property string $text
 * @property string $tags
 * @property int $rating
 * @property int $created_at
 */
class Story extends Model
{
    protected string $filePath = __DIR__ . '/../../database/stories.csv';

    /**
     * Директория загрузки файлов
     */
    public string $uploadPath = '/uploads/stories';

    /**
     * Возвращает связь пользователей
     */
    public function user(): mixed
    {
        return $this->hasOne(User::class, 'user_id');
    }

    /**
     * Возвращает связь пользователей
     */
/*    public function poll(): mixed
    {
        return $this->hasOne(Poll::class, 'id', 'post_id');
    }*/

    /**
     * Возвращает связь файлов
     *
     * @return mixed
     */
    public function files(): mixed
    {
        return $this->hasMany(File::class, 'id', 'post_id');
    }

    /**
     * Delete post
     *
     * @return int
     */
    public function delete(): int
    {
        foreach ($this->files() as $file) {
            $file->delete();
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
        $more = app(View::class)->fetch('app/_more', ['link' => '/' . $this->id]);

        if (str_contains($this->text, '[cut]')) {
            return bbCode(current(explode('[cut]', $this->text))) . $more;
        }

        if (Str::wordCount($this->text) > $words) {
            return bbCodeTruncate($this->text, $words) . $more;
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
}
