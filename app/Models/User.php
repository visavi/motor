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

    /**
     * The attributes that should be cast.
     */
    protected array $casts = [
        'confirmed'  => 'bool',
        'created_at' => 'int',
    ];

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
    public function getAvatar(string $size = 'sm'): string
    {
        if (! $this->id) {
            $path = '/assets/images/avatar_guest.png';
        } elseif ($this->avatar && file_exists(publicPath($this->avatar))) {
            $path = $this->avatar;
        } else {
            $path = '/assets/images/avatar_default.png';
        }

        return sprintf(
            '<span class="avatar avatar-%s" style="background-image: url(%s)"></span>',
            escape($size),
            escape($path),
        );
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
        return $this->role === User::BANNED;
    }

    /**
     * Check is pended
     *
     * @return bool
     */
    public function isPended(): bool
    {
        return $this->role === User::PENDED;
    }

    /**
     * Delete user
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
