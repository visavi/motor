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
 * @property string $picture
 * @property string $avatar
 * @property int $created_at
 */
class User extends Model
{
    protected string $filePath = __DIR__ . '/../../database/users.csv';

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

    /**
     * Get name
     *
     * @return string
     */
    public function getName(): string
    {
        if (! $this->id) {
            return setting('main.delete_name');
        }

        return escape($this->name ?? $this->login);
    }

    /**
     * Get profile link
     *
     * @return string
     */
    public function getProfile(): string
    {
        if (! $this->id) {
            return setting('main.delete_name');
        }

        return '<a href="/users/' . $this->login . '">' . $this->getName() . '</a>';
    }

    /**
     * Get avatar
     *
     * @return string
     */
    public function getAvatar(): string
    {
        if (! $this->id) {
            return '<img class="avatar-default rounded-circle" src="/assets/images/avatar_guest.png" alt="Аватар">';
        }

        if ($this->avatar && file_exists(publicPath($this->avatar))) {
            $avatar = '<img class="avatar-default rounded-circle" src="' . $this->avatar . '" alt="Аватар">';
        } else {
            $avatar = '<img class="avatar-default rounded-circle" src="/assets/images/avatar_default.png" alt="Аватар">';
        }

        return $avatar;
    }
}
