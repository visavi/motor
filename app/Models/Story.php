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
            $this->text = bbCode(current(explode('[cut]', $this->text))) . $more;
        } elseif (Str::wordCount($this->text) > $words) {
            $this->text = bbCodeTruncate($this->text, $words) . $more;
        }

        return $this->text;
    }
}
