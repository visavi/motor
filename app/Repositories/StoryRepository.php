<?php

declare(strict_types=1);

namespace App\Repositories;

//use App\Models\Story;
//use App\Models\Tag;
use App\Entities\Story;
use App\Services\Pagination;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use MotorORM\Collection;
use MotorORM\CollectionPaginate;
use Doctrine\ORM\EntityRepository;

class StoryRepository extends EntityRepository
{
    public function __construct(
        protected EntityManagerInterface $em,
        protected Pagination $pagination,
    ) {
        parent::__construct($em, $em->getClassMetadata(Story::class));
    }

    /**
     * Get by id
     *
     * @param int $id
     *
     * @return Story|null
     */
/*    public function getById(int $id): ?Story
    {
        return Story::query()->find($id);
    }*/

    /**
     * Get by slug
     *
     * @param string $slug
     *
     * @return Story|null
     */
/*    public function getBySlug(string $slug): ?Story
    {
        $data = explode('-', $slug);

        return Story::query()->find(end($data));
    }*/

    /**
     * Get stories
     *
     * @param int $perPage
     *
     *
     */
/*    public function getStories(int $perPage): CollectionPaginate
    {
        return Story::query()
            ->when(! isAdmin(), function (Story $query) {
                $query->active()
                    ->where('created_at', '<', time());
            })
            ->orderByDesc('locked')
            ->orderByDesc('created_at')
            ->with(['user', 'poll', 'comments', 'favorite', 'favorites'])
            ->paginate($perPage);
    }*/

    public function getStories(int $perPage)/*: CollectionPaginate*/
    {


        dd($this->createQueryBuilder('s'), $this->getClassMetadata(), get_class_methods($this), $this->pagination, $this);


        $story = $this->entityManager->getRepository(Story::class);
        $query = $story->createQueryBuilder('s')
            ->orderBy('s.locked', 'DESC')
            ->addOrderBy('s.created_at', 'DESC');

        if (! isAdmin()) {
            $query->andWhere('s.active = :active')
                ->andWhere('s.created_at < :datetime')
                ->setParameter('active', true)
                ->setParameter('datetime', new DateTime('now'));
        }

        return $this->pagination->paginate($query, $perPage);
    }

    /**
     * Get user stories
     *
     * @param int $userId
     * @param int $perPage
     *
     * @return CollectionPaginate
     */
    public function getStoriesByUserId(int $userId, int $perPage): CollectionPaginate
    {
        return Story::query()
            ->when(! isAdmin(), function (Story $query) {
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
     * @return CollectionPaginate<Story>
     */
    public function getStoriesByTag(string $tag, int $perPage): CollectionPaginate
    {
        $tags = Tag::query()
            ->where('tag', 'like', $tag)
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
     * @return CollectionPaginate<Story>
     */
    public function getStoriesBySearch(string $search, int $perPage): CollectionPaginate
    {
        return Story::query()
            ->active()
            ->where('created_at', '<', time())
            ->where('text', 'like', $search)
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
