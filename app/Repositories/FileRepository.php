<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\File;
use App\Models\Guestbook;
use MotorORM\Collection;

class FileRepository implements RepositoryInterface
{
    /**
     * @param int $id
     *
     * @return Guestbook|null
     */
    public function getById(int $id): ?File
    {
        return File::query()->find($id);
    }

    /**
     * @param int $userId
     * @param int $postId
     *
     * @return Collection<File>
     */
    public function getFiles(int $userId, int $postId): Collection
    {
        return File::query()
            ->where('user_id', $userId)
            ->where('post_id', $postId)
            ->get();
    }

    /**
     * @param int $postId
     *
     * @return Collection<File>
     */
    public function getFilesByPostId(int $postId): Collection
    {
        return File::query()
            ->where('post_id', $postId)
            ->get();
    }
}
