<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Class User
 *
 * @property int $id
 * @property string $login
 * @property string $password
 * @property string $email
 * @property string $role
 * @property string $name
 * @property int $created_at
 */
class User extends Model
{
    public string $filePath = __DIR__ . '/../../database/users.csv';

    public const BOSS   = 'boss';   // Владелец
    public const ADMIN  = 'admin';  // Админ
    public const MODER  = 'moder';  // Модератор
    public const EDITOR = 'editor'; // Редактор
    public const USER   = 'user';   // Пользователь
    public const PENDED = 'pended'; // Ожидающий
    public const BANNED = 'banned'; // Забаненный

    /**
     * All group
     */
    public const ALL_GROUP = [
        self::BOSS,
        self::ADMIN,
        self::MODER,
        self::EDITOR,
        self::USER,
        self::PENDED,
        self::BANNED,
    ];

    /**
     * Genders
     */
    public const MALE   = 'male';
    public const FEMALE = 'female';
}
