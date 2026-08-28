<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\BBCode;
use MotorORM\Query;
use MotorORM\Relation;

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
 * @property-read User $user
 * @method $this active()
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
        'active'     => 'bool',
        'user_id'    => 'int',
        'created_at' => 'int',
    ];

    /**
     * Возвращает связь пользователей
     *
     * @return Relation
     */
    public function user(): Relation
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    /**
     * Scope active
     *
     * @param Query $query
     *
     * @return Query
     */
    public function scopeActive(Query $query): Query
    {
        return $query->where('active', true);
    }
    /**
     * Get text
     *
     * @return string
     */
    public function getText(): string
    {
        return (new BBCode())->handle($this->text);
    }
}
