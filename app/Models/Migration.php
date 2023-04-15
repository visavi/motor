<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Class Migration
 *
 * @property int $id
 * @property string $name
 * @property int $batch
 */
class Migration extends Model
{
    /**
     * Table name
     */
    protected string $table = 'migrations';
}
