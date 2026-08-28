<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Class Poll
 *
 * @property int $id
 * @property int $user_id
 * @property int $entity_id
 * @property string $entity_name
 * @property string $vote
 * @property int $created_at
 */
class Poll extends Model
{
    /**
     * Table name
     */
    protected string $table = 'polls.csv';

    /**
     * The attributes that should be cast.
     */
    protected array $casts = [
        'user_id'    => 'int',
        'entity_id'  => 'int',
        'created_at' => 'int',
    ];

}
