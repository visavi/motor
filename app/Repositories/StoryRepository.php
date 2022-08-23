<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Story;
use MotorORM\Collection;
use MotorORM\CollectionPaginate;

class StoryRepository implements RepositoryInterface
{
    /**
     * Get by id
     *
     * @param int $id
     *
     * @return Story|null
     */
    public function getById(int $id): ?Story
    {
        return Story::query()->find($id);
    }

    /**
     * Get by slug
     *
     * @param string $slug
     *
     * @return Story|null
     */
    public function getBySlug(string $slug): ?Story
    {
        $data = explode('-', $slug);

        return Story::query()->find(end($data));
    }

    /**
     * Get posts
     *
     * @param int $perPage
     *
     * @return CollectionPaginate<Story>
     */
    public function getPosts(int $perPage): CollectionPaginate
    {
        return Story::query()
            ->orderByDesc('created_at')
            ->with(['user', 'poll'])
            ->paginate($perPage);
    }

    /**
     * Get all posts
     *
     * @return CollectionPaginate<Story>
     */
    public function getAllPosts(): Collection
    {
        return Story::query()->get();
    }

    /**
     * Get posts by tag
     *
     * @param string $tag
     * @param int    $perPage
     *
     * @return CollectionPaginate<Story>
     */
    public function getPostsByTag(string $tag, int $perPage): CollectionPaginate
    {
        return Story::query()
            ->where('tags', 'like', $tag)
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * Get popular tags
     *
     * @param int $count
     *
     * @return array
     */
    public function getPopularTags(int $count = 15): array
    {
        $tags = $this->getAllPosts()->pluck('tags', 'id');

        $allTags   = implode(',', $tags);
        $clearTags = preg_split('/\s*,\s*/', $allTags, -1, PREG_SPLIT_NO_EMPTY);
        $tags      = array_count_values($clearTags);

        arsort($tags);
        array_splice($tags, $count);

        return $tags;
    }
}
