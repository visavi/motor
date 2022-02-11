<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Guestbook;
use MotorORM\Collection;

class GuestbookRepository implements RepositoryInterface
{
    /**
     * @param int $perPage
     *
     * @return Collection
     */
    public function getMessages(int $perPage): Collection
    {
        return Guestbook::query()
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }
}
