<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Class Setting
 *
 * @property string $name
 * @property string $value
 */
class Setting extends Model
{
    /**
     * Table name
     */
    protected string $table = 'settings.csv';

    /**
     * The attributes that should be cast.
     */
    protected array $casts = [
        'name' => 'string',
    ];
}
