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
    protected string $filePath = __DIR__ . '/../../storage/database/polls.csv';
}
