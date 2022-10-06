<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\User;
use MotorORM\CollectionPaginate;

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
     * @return CollectionPaginate<User>
     */
    public function getUsers(int $perPage): CollectionPaginate
    {
        return User::query()
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }
}
