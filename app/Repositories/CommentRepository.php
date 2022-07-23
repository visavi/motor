<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Comment;

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
}
