<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Class Read
 *
 * @property int $id
 * @property int $story_id
 * @property string $ip
 * @property int $created_at
 */
class Read extends Model
{
    protected string $filePath = __DIR__ . '/../../database/reads.csv';
}
