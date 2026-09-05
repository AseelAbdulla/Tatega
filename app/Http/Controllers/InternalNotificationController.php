<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInternalNotificationRequest;
use App\Http\Requests\UpdateInternalNotificationRequest;
use App\Models\InternalNotification;
use App\Services\InternalNotificationService;
use Illuminate\Http\Request;

class InternalNotificationController extends Controller
{


    protected $notificationService;



    public function __construct(InternalNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }




    /**
     * جلب عدد الإشعارات غير المقروءة للعميل الحالي
     */
    public function unreadCount(Request $request)
    {
        $count = InternalNotification::where('user_id', $request->user()->id)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'unread_count' => $count
        ]);
    }



    /**
     * Display all notifications for current user
     */
    public function index(Request $request)
    {
        $notifications = InternalNotification::where('user_id', $request->user()->id)
            ->latest()
            ->paginate(15);

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


    public function markAsRead(Request $request, $id)
    {
        $notification = $request->user()->notifications()->find($id);

        if ($notification) {
            $notification->update(['is_read' => true]);
        }

        return response()->json(['status' => true, 'message' => 'Marked as read']);
    }

    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications()->update(['is_read' => true]);

        return response()->json(['status' => true, 'message' => 'All marked as read']);
    }

    /**
     * Update notification
     */
    public function update(UpdateInternalNotificationRequest $request, string $id)
    {


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
}
