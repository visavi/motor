<?php

declare(strict_types=1);

namespace App\Models;

use MotorORM\Relation;

/**
 * Class Favorite
 */
class Favorite extends Model
{
    /**
     * Table name
     */
    protected string $table = 'favorites.csv';

    /**
     * The attributes that should be cast.
     */
    protected array $casts = [
        'user_id'    => 'int',
        'story_id'   => 'int',
        'created_at' => 'int',
    ];

    /**
     * Возвращает связь статьи
     *
     * @return Relation
     */
    public function story(): Relation
    {
        return $this->hasOne(Story::class, 'id', 'story_id');
    }

}
