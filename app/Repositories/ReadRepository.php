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
     * @param int    $postId
     * @param string $ip
     *
     * @return bool
     */
    public function existsRead(int $postId, string $ip): bool
    {
        return Read::query()->where('post_id', $postId)->where('ip', $ip)->exists();
    }

    /**
     * Create read
     *
     * @param Story  $post
     * @param string $ip
     *
     * @return void
     */
    public function createRead(Story $post, string $ip): void
    {
        if ($this->existsRead($post->id, $ip)) {
            return;
        }

        $post->update([
            'reads' => $post->reads + 1,
        ]);

        Read::query()->insert([
            'post_id'    => $post->id,
            'ip'         => $ip,
            'created_at' => time(),
        ]);
    }
}
