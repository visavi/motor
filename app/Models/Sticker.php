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
    /**
     * Table name
     */
    protected string $table = 'stickers';

    /**
     * The attributes that should be cast.
     */
    protected array $casts = [
        'code' => 'string',
    ];
}
