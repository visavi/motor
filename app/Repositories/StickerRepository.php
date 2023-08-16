<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Sticker;
use MotorORM\Collection;

class StickerRepository implements RepositoryInterface
{
    /**
     * @param int $id
     *
     * @return Sticker
     */
    public function getById(int $id): Sticker
    {
        return Sticker::query()->find($id);
    }

    /**
     * Get stickers
     *
     * @param int $perPage
     *
     * @return Collection<Sticker>
     */
    public function getStickers(int $perPage): Collection
    {
        return Sticker::query()
            ->orderBy('code')
            ->paginate($perPage);
    }

    /**
     * Create sticker
     *
     * @param array $data
     *
     * @return Sticker
     */
    public function create(array $data): Sticker
    {
        return Sticker::query()->create($data);
    }
}
