<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Guestbook;
use MotorORM\CollectionPaginate;

class GuestbookRepository implements RepositoryInterface
{
    /**
     * @param int $id
     *
     * @return Guestbook|null
     */
    public function getById(int $id): ?Guestbook
    {
        return Guestbook::query()->find($id);
    }

    /**
     * Get count messages
     *
     * @return int
     */
    public function getCount(): int
    {
        return Guestbook::query()->count();
    }

    /**
     * @param int $perPage
     *
     * @return CollectionPaginate<Guestbook>
     */
    public function getMessages(int $perPage): CollectionPaginate
    {
        return Guestbook::query()
            ->when(! isAdmin(), function (Guestbook $query) {
                $query->active();
            })
            ->orderByDesc('created_at')
            ->with('user')
            ->paginate($perPage);
    }
}
