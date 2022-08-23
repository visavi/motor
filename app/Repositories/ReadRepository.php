<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Read;
use App\Models\Story;

class ReadRepository implements RepositoryInterface
{
    /**
     * Exists read
     *
     * @param int    $storyId
     * @param string $ip
     *
     * @return bool
     */
    public function existsRead(int $storyId, string $ip): bool
    {
        return Read::query()->where('story_id', $storyId)->where('ip', $ip)->exists();
    }

    /**
     * Create read
     *
     * @param Story  $story
     * @param string $ip
     *
     * @return void
     */
    public function createRead(Story $story, string $ip): void
    {
        if ($this->existsRead($story->id, $ip)) {
            return;
        }

        $story->update([
            'reads' => $story->reads + 1,
        ]);

        Read::query()->insert([
            'story_id'   => $story->id,
            'ip'         => $ip,
            'created_at' => time(),
        ]);
    }
}
