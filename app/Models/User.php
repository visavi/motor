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
 * @property bool $confirmed
 * @property string $confirm_code
 * @property int $created_at
 */
class User extends Model
{
    /**
     * Table name
     */
    protected string $table = 'users.csv';

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
    public const ROLES = [
        self::BOSS,
        self::ADMIN,
        self::MODER,
        self::EDITOR,
        self::USER,
        self::PENDED,
        self::BANNED,
    ];

    /**
     * All group
     */
    public const ADMIN_ROLES = [
        self::BOSS,
        self::ADMIN,
        self::MODER,
        self::EDITOR,
    ];

    /**
     * Genders
     */
    public const MALE   = 'male';
    public const FEMALE = 'female';

    /**
     * The attributes that should be cast.
     */
    protected array $casts = [
        'confirmed' => 'bool',
    ];

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

    /**
     * Get role
     *
     * @return string
     */
    public function getRole(): string
    {
        if (! $this->id) {
            return setting('roles.user');
        }

        return setting('roles.' . $this->role);
    }

    /**
     * Check is banned
     *
     * @return bool
     */
    public function isBanned(): bool
    {
        return $this->role === self::BANNED;
    }

    /**
     * Check is pended
     *
     * @return bool
     */
    public function isPended(): bool
    {
        return $this->role === self::PENDED;
    }

    /**
     * Delete story
     *
     * @return int
     */
    public function delete(): int
    {
        // delete photo
        if ($this->picture && file_exists(publicPath($this->picture))) {
            unlink(publicPath($this->picture));
        }

        // delete avatar
        if ($this->avatar && file_exists(publicPath($this->avatar))) {
            unlink(publicPath($this->avatar));
        }

        return parent::delete();
    }
}
