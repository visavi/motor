<?php

declare(strict_types=1);

namespace App\Models;

use MotorORM\Collection;

/**
 * Class Favorite
 *
 * @property int $id
 * @property string $user_id
 * @property string $story_id
 * @property int $created_at
 */
class Favorite extends Model
{
    protected string $filePath = __DIR__ . '/../../database/favorites.csv';
}
