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
     * @param int $perPage
     *
     * @return CollectionPaginate<Guestbook>
     */
    public function getMessages(int $perPage): CollectionPaginate
    {
        return Guestbook::query()
            ->orderByDesc('created_at')
            ->with('user')
            ->paginate($perPage);
    }
}
