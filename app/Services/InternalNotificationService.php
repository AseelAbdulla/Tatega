<?php

namespace App\Services;

use App\Models\InternalNotification;

class InternalNotificationService
{
    /**
     * =========================================================
     * ADMIN
     * =========================================================
     */

    /**
     * Get notifications for current user
     */
    public function getAllNotifications()
    {
        return InternalNotification::with('user:id,name')
            ->where('user_id', auth()->id())
            ->latest()
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


    /**
     * =========================================================
     * CUSTOMER
     * =========================================================
     */

    /**
     * Get notifications for authenticated customer
     *
     * العميل يرى إشعاراته فقط
     */
    public function getCustomerNotifications()
    {
        return InternalNotification::query()
            ->where('user_id', auth()->id())
            ->latest()
            ->get();
    }


    /**
     * Get one notification for authenticated customer
     *
     * يمنع العميل من مشاهدة إشعار مستخدم آخر
     */
    public function getCustomerNotificationById($id)
    {
        return InternalNotification::query()
            ->where('user_id', auth()->id())
            ->find($id);
    }


    /**
     * Mark one customer notification as read
     *
     * العميل يستطيع تحديث إشعاره هو فقط
     */
    public function markCustomerNotificationAsRead($id)
    {
        $notification = InternalNotification::query()
            ->where('user_id', auth()->id())
            ->find($id);

        if (!$notification) {
            return null;
        }

        $notification->update([
            'is_read' => true,
        ]);

        return $notification->fresh();
    }


    /**
     * Mark all customer notifications as read
     */
    public function markAllCustomerNotificationsAsRead()
    {
        return InternalNotification::query()
            ->where('user_id', auth()->id())
            ->where('is_read', false)
            ->update([
                'is_read' => true,
            ]);
    }


    /**
     * Get unread customer notifications count
     *
     * هذا سنستخدمه لاحقًا لرقم 🔔
     */
    public function getUnreadCustomerNotificationsCount()
    {
        return InternalNotification::query()
            ->where('user_id', auth()->id())
            ->where('is_read', false)
            ->count();
    }
}
