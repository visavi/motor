<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Class Sticker
 *
 * @property string $login
 * @property string $path
 */
class Sticker extends Model
{
    protected string $filePath = __DIR__ . '/../../database/stickers.csv';
}
