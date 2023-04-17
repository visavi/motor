<?php

declare(strict_types=1);

namespace App\Models;

use MotorORM\Builder;

/**
 * Class Favorite
 *
 * @property int $id
 * @property string $user_id
 * @property string $story_id
 * @property int $created_at
 *
 * @property-read Story $story
 */
class Favorite extends Model
{
    /**
     * Table name
     */
    protected string $table = 'favorites.csv';

    /**
     * Возвращает связь статьи
     *
     * @return Builder
     */
    public function story(): Builder
    {
        return $this->hasOne(Story::class, 'id', 'story_id');
    }
}
