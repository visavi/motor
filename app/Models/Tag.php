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
    protected string $filePath = __DIR__ . '/../../storage/database/tags.csv';
}
