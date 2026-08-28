<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Story;
use App\Models\Tag;
use MotorORM\Collection;
use MotorORM\Pagination;
use MotorORM\Query;

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
     * Get stories
     *
     * @param int $perPage
     *
     * @return Pagination<Story>
     */
    public function getStories(int $perPage): Pagination
    {
        return Story::query()
            ->when(! isAdmin(), function (Query $query) {
                $query->active()
                    ->where('created_at', '<', time());
            })
            ->orderByDesc('locked')
            ->orderByDesc('created_at')
            ->with(['user', 'poll', 'comments', 'favorite', 'favorites'])
            ->paginate($perPage);
    }

    /**
     * Get user stories
     *
     * @param int $userId
     * @param int $perPage
     *
     * @return Pagination
     */
    public function getStoriesByUserId(int $userId, int $perPage): Pagination
    {
        return Story::query()
            ->when(! isAdmin(), function (Query $query) {
                $query->where('created_at', '<', time());
            })
            ->where('user_id', $userId)
            ->orderByDesc('locked')
            ->orderByDesc('created_at')
            ->with(['user', 'poll', 'comments', 'favorite', 'favorites'])
            ->paginate($perPage);
    }

    /**
     * Get stories by tag
     *
     * @param string $tag
     * @param int    $perPage
     *
     * @return Pagination<Story>
     */
    public function getStoriesByTag(string $tag, int $perPage): Pagination
    {
        $tags = Tag::query()
            ->whereLike('tag', $tag)
            ->get()
            ->pluck('story_id')
            ->all();

        return Story::query()
            ->active()
            ->where('created_at', '<', time())
            ->whereIn('id', $tags)
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * Get stories by search
     *
     * @param string $search
     * @param int    $perPage
     *
     * @return Pagination<Story>
     */
    public function getStoriesBySearch(string $search, int $perPage): Pagination
    {
        return Story::query()
            ->active()
            ->where('created_at', '<', time())
            ->whereLike('text', $search)
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->appends(['search' => $search]);
    }

    /**
     * Get active stories
     *
     * @return Collection<Story>
     */
    public function getActiveStories(): Collection
    {
        return Story::query()
            ->active()
            ->where('created_at', '<', time())
            ->get();
    }

    /**
     * Get count stories
     *
     * @return int
     */
    public function getCount(): int
    {
        return Story::query()
            ->active()
            ->where('created_at', '<', time())
            ->count();
    }
}
