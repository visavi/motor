<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Story;
use MotorORM\CollectionPaginate;

class StoryRepository implements RepositoryInterface
{
    /**
     * @param int $id
     *
     * @return Story|null
     */
    public function getById(int $id): ?Story
    {
        return Story::query()->find($id);
    }

    /**
     * @param int $perPage
     *
     * @return CollectionPaginate<Story>
     */
    public function getPosts(int $perPage): CollectionPaginate
    {
        return Story::query()
            ->orderByDesc('created_at')
            //->with(['user', 'poll'])
            ->paginate($perPage);
    }
}
