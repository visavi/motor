<?php

declare(strict_types=1);

namespace App\Models;

use MotorORM\Builder;

/**
 * Class Guestbook
 *
 * @property int $id
 * @property string $user_id
 * @property string $text
 * @property string $name
 * @property bool $active
 * @property int $created_at
 *
 * @method $this active()
 *
 * @property-read User $user
 */
class Guestbook extends Model
{
    /**
     * Table name
     */
    protected string $table = 'guestbook.csv';

    /**
     * The attributes that should be cast.
     */
    protected array $casts = [
        'active' => 'bool',
    ];

    /**
     * Возвращает связь пользователей
     *
     * @return Builder
     */
    public function user(): Builder
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    /**
     * Scope active
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }
}
