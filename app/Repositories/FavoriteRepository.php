<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Favorite;
use MotorORM\Collection;

class FavoriteRepository implements RepositoryInterface
{
    /**
     * @param int $id
     *
     * @return Favorite
     */
    public function getById(int $id): Favorite
    {
        return Favorite::query()->find($id);
    }

    /**
     * Get user Favorites
     *
     * @param int $perPage
     *
     * @return Collection<Favorite>
     */
    public function getFavorites(int $perPage): Collection
    {
        return Favorite::query()
            ->where('user_id', getUser('id'))
            ->orderByDesc('created_at')
            ->with('story')
            ->paginate($perPage);
    }
}
