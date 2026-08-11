<?php

namespace App\Services;

use App\Models\InternalNotification;

class InternalNotificationService
{
    /**
     * Get notifications for current user
     */
    public function getAllNotifications()
    {
        return InternalNotification::with('user:id,name')
            ->where('user_id', auth()->id())
            ->get();
    }

    /**
     * Create notification
     * Admin only
     */
    public function createNotification(array $data)
    {
        return InternalNotification::create([
            'user_id' => $data['user_id'],
            'title' => $data['title'],
            'message' => $data['message'],
            'type' => $data['type'],
            'is_read' => false,
            'sent_at' => $data['sent_at'] ?? now(),
        ]);
    }

    /**
     * Get notification by id
     */
    public function getNotificationById($id)
    {
        return InternalNotification::with('user:id,name')
            ->where('user_id', auth()->id())
            ->find($id);
    }

    /**
     * Update notification
     * Admin only
     */
    public function updateNotification($id, array $data)
    {
        $notification = InternalNotification::find($id);

        if (!$notification) {
            return null;
        }

        $notification->update([
            'title' => $data['title'] ?? $notification->title,
            'message' => $data['message'] ?? $notification->message,
            'type' => $data['type'] ?? $notification->type,
            'is_read' => $data['is_read'] ?? $notification->is_read,
            'sent_at' => $data['sent_at'] ?? $notification->sent_at,
        ]);

        return $notification;
    }

    /**
     * Delete notification
     * Admin only
     */
    public function deleteNotification($id)
    {
        $notification = InternalNotification::find($id);

        if (!$notification) {
            return false;
        }

        $notification->delete();

        return true;
    }
}