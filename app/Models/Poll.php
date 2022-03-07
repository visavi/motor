<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Class Poll
 *
 * @property int $id
 * @property int $user_id
 * @property int $post_id
 * @property string $vote
 * @property int $created_at
 */
class Poll extends Model
{
    protected string $filePath = __DIR__ . '/../../database/polls.csv';
}
