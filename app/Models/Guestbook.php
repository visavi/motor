<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Class Guestbook
 *
 * @property int $id
 * @property string $user_id
 * @property string $title
 * @property string $text
 * @property int $file_id
 * @property int $created_at
 */
class Guestbook extends Model
{
    protected string $filePath = __DIR__ . '/../../database/guestbook.csv';

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
