<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\User;
use MotorORM\Collection;
use MotorORM\Pagination;

class UserRepository implements RepositoryInterface
{
    /**
     * Get by id
     *
     * @param int $id
     *
     * @return User
     */
    public function getById(int $id): User
    {
        return User::query()->find($id);
    }

    /**
     * Get count users
     *
     * @return int
     */
    public function getCount(): int
    {
        return User::query()->count();
    }

    /**
     * Get stories
     *
     * @param int $perPage
     *
     * @return Pagination<User>
     */
    public function getUsers(int $perPage): Pagination
    {
        return User::query()
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * Возвращает пользователей, заходивших за последние минуты
     *
     * @param int $minutes
     *
     * @return Collection<User>
     */
    public function getOnline(int $minutes = 15, int $limit = 10): Collection
    {
        return User::query()
            ->where('online_at', '>', time() - $minutes * 60)
            ->orderByDesc('online_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Возвращает число пользователей, заходивших за последние минуты
     *
     * @param int $minutes
     *
     * @return int
     */
    public function getOnlineCount(int $minutes = 15): int
    {
        return User::query()
            ->where('online_at', '>', time() - $minutes * 60)
            ->count();
    }
}
