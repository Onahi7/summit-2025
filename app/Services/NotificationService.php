<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

class NotificationService
{
    public function create(array $data)
    {
        $notification = Notification::create($data);

        if (isset($data['user_id'])) {
            $this->broadcastToUser($notification, $data['user_id']);
        } elseif (isset($data['role'])) {
            $this->broadcastToRole($notification, $data['role']);
        } else {
            $this->broadcastToAll($notification);
        }

        return $notification;
    }

    public function broadcastToUser(Notification $notification, $userId)
    {
        Broadcast::private('App.Models.User.' . $userId)
            ->event('NotificationCreated', ['notification' => $notification]);
    }

    public function broadcastToRole(Notification $notification, $role)
    {
        $users = User::where('role', $role)->get();
        foreach ($users as $user) {
            $this->broadcastToUser($notification, $user->id);
        }
    }

    public function broadcastToAll(Notification $notification)
    {
        Broadcast::channel('notifications')
            ->event('NotificationCreated', ['notification' => $notification]);
    }

    public function markAsRead($notificationId, User $user)
    {
        $notification = Notification::findOrFail($notificationId);
        if ($notification->user_id === $user->id || $notification->role === $user->role || !$notification->role) {
            $notification->markAsRead();
        }
        return $notification;
    }

    public function markAllAsRead(User $user)
    {
        Notification::forUser($user)
            ->unread()
            ->update(['read_at' => now()]);
    }

    public function getUnreadCount(User $user)
    {
        return Notification::forUser($user)
            ->unread()
            ->count();
    }

    public function getNotifications(User $user, $perPage = 10)
    {
        return Notification::forUser($user)
            ->latest()
            ->paginate($perPage);
    }
}
