<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Class Tag
 *
 * @property int $id
 * @property string $value
 */
class Tag extends Model
{
    /**
     * Table name
     */
    protected string $table = 'tags.csv';

    /**
     * The attributes that should be cast.
     */
    protected array $casts = [
        'story_id' => 'int',
    ];

}
