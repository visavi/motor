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
     * Create comment
     *
     * @param array $data
     *
     * @return Comment
     */
    public function create(array $data): Comment
    {
        return Comment::query()->create($data);
    }

    /**
     * Get by id and story id
     *
     * @param int $id
     * @param int $storyId
     *
     * @return Comment|null
     */
    public function getByIdAndStoryId(int $id, int $storyId): ?Comment
    {
        return Comment::query()
            ->where('id', $id)
            ->where('story_id', $storyId)
            ->first();
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
            ->with('story')
            ->get();
    }

    /**
     * Get best comments
     *
     * @param int $count
     *
     * @return Collection<Comment>
     */
    public function getBestComments(int $count = 5): Collection
    {
        return Comment::query()
            ->orderByDesc('rating')
            ->limit($count)
            ->with('story')
            ->get();
    }
}
