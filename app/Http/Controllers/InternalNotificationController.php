<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInternalNotificationRequest;
use App\Http\Requests\UpdateInternalNotificationRequest;
use App\Services\InternalNotificationService;

class InternalNotificationController extends Controller
{
    protected $notificationService;


    public function __construct(
        InternalNotificationService $notificationService
    ) {
        $this->notificationService = $notificationService;
    }


    /**
     * =========================================================
     * ADMIN
     * =========================================================
     */


    /**
     * Display all notifications
     */
    public function index()
    {
        $notifications = $this->notificationService
            ->getAllNotifications();

        return response()->json([
            'status' => true,
            'data' => $notifications
        ]);
    }


    /**
     * Store notification
     */
    public function store(StoreInternalNotificationRequest $request)
    {
        $notification = $this->notificationService
            ->createNotification(
                $request->validated()
            );

        return response()->json([
            'message' => 'Notification created successfully',
            'data' => $notification
        ], 201);
    }


    /**
     * Display specific notification
     */
    public function show(string $id)
    {
        $notification = $this->notificationService
            ->getNotificationById($id);

        if (!$notification) {
            return response()->json([
                'message' => 'Notification not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $notification
        ]);
    }


    /**
     * Update notification
     */
    public function update(
        UpdateInternalNotificationRequest $request,
        string $id
    ) {
        $notification = $this->notificationService
            ->updateNotification(
                $id,
                $request->validated()
            );

        if (!$notification) {
            return response()->json([
                'message' => 'Notification not found'
            ], 404);
        }

        return response()->json([
            'message' => 'Notification updated successfully',
            'data' => $notification
        ]);
    }


    /**
     * Delete notification
     */
    public function destroy(string $id)
    {
        $deleted = $this->notificationService
            ->deleteNotification($id);

        if (!$deleted) {
            return response()->json([
                'message' => 'Notification not found'
            ], 404);
        }

        return response()->json([
            'message' => 'Notification deleted successfully'
        ]);
    }


    /**
     * =========================================================
     * CUSTOMER
     * =========================================================
     */


    /**
     * Get customer notifications
     */
    public function customerIndex()
    {
        $notifications = $this->notificationService
            ->getCustomerNotifications();

        return response()->json([
            'status' => true,
            'data' => $notifications
        ]);
    }


    /**
     * Get one customer notification
     */
    public function customerShow(string $id)
    {
        $notification = $this->notificationService
            ->getCustomerNotificationById($id);

        if (!$notification) {
            return response()->json([
                'status' => false,
                'message' => 'Notification not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $notification
        ]);
    }


    /**
     * Mark one customer notification as read
     */
    public function markAsRead(string $id)
    {
        $notification = $this->notificationService
            ->markCustomerNotificationAsRead($id);

        if (!$notification) {
            return response()->json([
                'status' => false,
                'message' => 'Notification not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Notification marked as read',
            'data' => $notification
        ]);
    }


    /**
     * Mark all customer notifications as read
     */
    public function markAllAsRead()
    {
        $count = $this->notificationService
            ->markAllCustomerNotificationsAsRead();

        return response()->json([
            'status' => true,
            'message' => 'All notifications marked as read',
            'updated_count' => $count
        ]);
    }


    /**
     * Get unread notifications count
     *
     * سيتم استخدامه لاحقًا في 🔔
     */
    public function unreadCount()
    {
        $count = $this->notificationService
            ->getUnreadCustomerNotificationsCount();

        return response()->json([
            'status' => true,
            'count' => $count
        ]);
    }
}
