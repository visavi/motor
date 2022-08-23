<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Comment;
use MotorORM\Collection;

class CommentRepository implements RepositoryInterface
{
    /**
     * @param int $id
     *
     * @return Comment|null
     */
    public function getById(int $id): ?Comment
    {
        return Comment::query()->find($id);
    }

    /**
     * Get last comments
     *
     * @param int $count
     *
     * @return Collection<Comment>
     */
    public function getLastComments(int $count = 5): Collection
    {
        return Comment::query()
            ->orderByDesc('created_at')
            ->limit($count)
            ->with('post')
            ->get();
    }
}
