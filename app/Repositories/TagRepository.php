<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Favorite;
use App\Models\Story;
use App\Models\Tag;
use MotorORM\Collection;
use MotorORM\CollectionPaginate;

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
        array_splice($tags, $count);

        return $tags;
    }
}
