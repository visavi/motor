<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Notification;
use App\Models\User;
use MotorORM\CollectionPaginate;

class NotificationRepository implements RepositoryInterface
{
    /**
     * Get by id
     *
     * @param int $id
     *
     * @return Notification|null
     */
    public function getUserNotificationById(int $id): ?Notification
    {
        return Notification::query()
            ->where('id', $id)
            ->where('user_id', getUser('id'))
            ->first();
    }

    /**
     * @return CollectionPaginate<Notification>
     */
    public function getNotifications(): CollectionPaginate
    {
        return Notification::query()
            ->where('user_id', getUser('id'))
            ->orderByDesc('created_at')
            ->paginate();
    }

    /**
     * Get count messages
     *
     * @return int
     */
    public function getCount(): int
    {
        return Notification::query()
            ->where('user_id', getUser('id'))
            ->where('read', 0)
            ->count();
    }

    /**
     * Mark as read
     *
     * @return void
     */
    public function markAsRead(): void
    {
        Notification::query()
            ->where('user_id', getUser('id'))
            ->where('read', 0)
            ->update([
                'read' => true,
            ]);
    }

    /**
     * Create notification
     *
     * @param User $user
     * @param string $message
     *
     * @return Notification
     */
    public function createNotification(User $user, string $message): Notification
    {
        return Notification::query()->create([
            'user_id'    => $user->id,
            'message'    => $message,
            'read'       => false,
            'created_at' => time(),
        ]);
    }
}
