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
     * @param int $storyId
     *
     * @return Collection<File>
     */
    public function getFiles(int $userId, int $storyId): Collection
    {
        return File::query()
            ->where('user_id', $userId)
            ->where('story_id', $storyId)
            ->get();
    }

    /**
     * @param int $storyId
     *
     * @return Collection<File>
     */
    public function getFilesByStoryId(int $storyId): Collection
    {
        return File::query()
            ->where('story_id', $storyId)
            ->get();
    }
}
