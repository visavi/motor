<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Class File
 *
 * @property int $id
 * @property int $user_id
 * @property int $story_id
 * @property string $path
 * @property string $name
 * @property string $ext
 * @property int $size
 * @property int $created_at
 */
class File extends Model
{
    /**
     * The attributes that should be cast.
     */
    protected array $casts = [
        'size' => 'int',
    ];

    protected string $filePath = __DIR__ . '/../../database/files.csv';

    /**
     * Является ли файл картинкой
     *
     * @return bool
     */
    public function isImage(): bool
    {
        return in_array($this->ext, ['jpg', 'jpeg', 'gif', 'png'], true);
    }

    /**
     * Является ли файл аудио
     *
     * @return bool
     */
    public function isAudio(): bool
    {
        return $this->ext === 'mp3';
    }

    /**
     * Delete file
     *
     * @return int
     */
    public function delete(): int
    {
        if (file_exists(publicPath($this->path))) {
            unlink(publicPath($this->path));
        }

        return parent::delete();
    }
}
