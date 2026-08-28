<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInternalNotificationRequest;
use App\Http\Requests\UpdateInternalNotificationRequest;
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
        $count = $request->user()
            ->unreadNotifications()
            ->count();

        return response()->json([
            'unread_count' => $count
        ]);
    }

    /**
     * Display all notifications
     */
    public function index(Request $request)
    {

    $notifications = $request->user()
            ->notifications()
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

        ],201);


    }









    /**
     * Display specific notification
     */
    public function show(string $id)
    {


        $notification = $this->notificationService
            ->getNotificationById($id);




        if(!$notification)
        {

            return response()->json([

                'message' => 'Notification not found'

            ],404);

        }





        return response()->json([

            'status' => true,

            'data' => $notification

        ]);

    }












    /**
     * Update notification
     */
    public function update(UpdateInternalNotificationRequest $request,string $id)
    {


        $notification = $this->notificationService
            ->updateNotification(

                $id,

                $request->validated()

            );







        if(!$notification)
        {

            return response()->json([

                'message' => 'Notification not found'

            ],404);

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





        if(!$deleted)
        {

            return response()->json([

                'message' => 'Notification not found'

            ],404);

        }







        return response()->json([

            'message' => 'Notification deleted successfully'

        ]);

    }


}
