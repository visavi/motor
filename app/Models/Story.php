<?php

declare(strict_types=1);

namespace App\Models;

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
     *
     * @var string
     */
    public $uploadPath = '/uploads/stories';

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
     * Delete message
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
}
