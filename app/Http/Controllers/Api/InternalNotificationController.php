<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InternalNotification;
use Illuminate\Support\Facades\Validator;

class InternalNotificationController extends Controller
{

    /**
     * Display all notifications
     */
    public function index()
    {
        $notifications = InternalNotification::with('user:id,name')
                            ->get();


        return response()->json([
            'status'=>true,
            'data'=>$notifications
        ]);
    }




    /**
     * Store notification
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [

            'user_id'=>'nullable|exists:users,id',

            'title'=>'required|array',

            'message'=>'required|array',

            'type'=>'required|string|max:100',

            'is_read'=>'nullable|boolean',

            'sent_at'=>'nullable|date'

        ]);



        if($validator->fails()){

            return response()->json([
                'errors'=>$validator->errors()
            ],422);

        }



        $notification = InternalNotification::create([

            'user_id'=>$request->user_id,

            'title'=>$request->title,

            'message'=>$request->message,

            'type'=>$request->type,

            'is_read'=>$request->is_read ?? false,

            'sent_at'=>$request->sent_at

        ]);



        return response()->json([

            'message'=>'Notification created successfully',

            'data'=>$notification

        ],201);

    }




    /**
     * Display specific notification
     */
    public function show(string $id)
    {

        $notification = InternalNotification::with('user:id,name')
                            ->find($id);



        if(!$notification){

            return response()->json([
                'message'=>'Notification not found'
            ],404);

        }



        return response()->json([

            'status'=>true,

            'data'=>$notification

        ]);

    }





    /**
     * Update notification
     */
    public function update(Request $request,string $id)
    {

        $notification = InternalNotification::find($id);



        if(!$notification){

            return response()->json([
                'message'=>'Notification not found'
            ],404);

        }



        $validator = Validator::make($request->all(), [

            'title'=>'nullable|array',

            'message'=>'nullable|array',

            'type'=>'nullable|string|max:100',

            'is_read'=>'nullable|boolean',

            'sent_at'=>'nullable|date'

        ]);



        if($validator->fails()){

            return response()->json([
                'errors'=>$validator->errors()
            ],422);

        }




        $notification->update([

            'title'=>$request->title ?? $notification->title,

            'message'=>$request->message ?? $notification->message,

            'type'=>$request->type ?? $notification->type,

            'is_read'=>$request->is_read ?? $notification->is_read,

            'sent_at'=>$request->sent_at ?? $notification->sent_at

        ]);



        return response()->json([

            'message'=>'Notification updated successfully',

            'data'=>$notification

        ]);

    }





    /**
     * Delete notification
     */
    public function destroy(string $id)
    {

        $notification = InternalNotification::find($id);



        if(!$notification){

            return response()->json([
                'message'=>'Notification not found'
            ],404);

        }



        $notification->delete();



        return response()->json([

            'message'=>'Notification deleted successfully'

        ]);

    }

}
