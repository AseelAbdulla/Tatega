<?php

namespace App\Services;

use App\Models\InternalNotification;


class InternalNotificationService
{


    /**
     * Get all notifications
     */
    public function getAllNotifications()
    {

        return InternalNotification::with('user:id,name')
            ->get();

    }







    /**
     * Create notification
     */
    public function createNotification(array $data)
    {

        return InternalNotification::create([

            'user_id' => $data['user_id'] ?? null,

            'title' => $data['title'],

            'message' => $data['message'],

            'type' => $data['type'],

            'is_read' => $data['is_read'] ?? false,

            'sent_at' => $data['sent_at'] ?? null

        ]);

    }








    /**
     * Get notification by id
     */
    public function getNotificationById($id)
    {

        return InternalNotification::with('user:id,name')
            ->find($id);

    }








    /**
     * Update notification
     */
    public function updateNotification($id,array $data)
    {

        $notification = InternalNotification::find($id);



        if(!$notification)
        {
            return null;
        }




        $notification->update([

            'title' => $data['title'] ?? $notification->title,

            'message' => $data['message'] ?? $notification->message,

            'type' => $data['type'] ?? $notification->type,

            'is_read' => $data['is_read'] ?? $notification->is_read,

            'sent_at' => $data['sent_at'] ?? $notification->sent_at

        ]);



        return $notification;

    }








    /**
     * Delete notification
     */
    public function deleteNotification($id)
    {

        $notification = InternalNotification::find($id);



        if(!$notification)
        {
            return false;
        }



        $notification->delete();



        return true;

    }


}
