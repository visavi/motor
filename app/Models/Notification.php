<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Class Notification
 *
 * @property int $id
 * @property int $user_id
 * @property string $message
 * @property bool $read
 * @property int $created_at
 */
class Notification extends Model
{
    /**
     * Table name
     */
    protected string $table = 'notifications.csv';

    /**
     * The attributes that should be cast.
     */
    protected array $casts = [
        'read'  => 'bool',
    ];
}
