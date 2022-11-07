<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Tag;

class TagRepository implements RepositoryInterface
{
    /**
     * Get popular tags
     *
     * @param int $count
     *
     * @return array
     */
    public function getPopularTags(int $count = 15): array
    {
        $tags = Tag::query()->get()->pluck('tag');
        $tags = array_count_values($tags);

        arsort($tags);
        return array_slice($tags, 0, $count, true);
    }
}
