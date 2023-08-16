<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Class Sticker
 *
 * @property int $id
 * @property string $code
 * @property string $path
 */
class Sticker extends Model
{
    /**
     * Table name
     */
    protected string $table = 'stickers.csv';

    /**
     * Директория загрузки файлов
     */
    public string $uploadPath = '/uploads/stickers';
}
