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
     * Audio extensions
     */
    public const AUDIO = ['mp3'];

    /**
     * Video extensions
     */
    public const VIDEO = ['mp4'];

    /**
     * Image extensions
     */
    public const IMAGES = ['jpg', 'jpeg', 'gif', 'png', 'bmp', 'webp'];

    /**
     * The attributes that should be cast.
     */
    protected array $casts = [
        'size' => 'int',
    ];

    protected string $filePath = __DIR__ . '/../../storage/database/files.csv';

    /**
     * Является ли файл картинкой
     *
     * @return bool
     */
    public function isImage(): bool
    {
        return in_array($this->ext, self::IMAGES, true);
    }

    /**
     * Является ли файл аудио
     *
     * @return bool
     */
    public function isAudio(): bool
    {
        return in_array($this->ext, self::AUDIO, true);
    }

    /**
     * Является ли файл видео
     *
     * @return bool
     */
    public function isVideo(): bool
    {
        return in_array($this->ext, self::VIDEO, true);
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
