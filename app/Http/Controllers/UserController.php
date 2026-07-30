<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index()
    {
        $users = User::with('roles')
            ->select('id', 'name', 'email', 'phone', 'status')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $users
        ]);
    }


    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|unique:users,phone',
            'password' => 'required|min:8',
            'role_id' => 'required|exists:roles,id'

        ]);


        if ($validator->fails()) {

            return response()->json([
                'errors' => $validator->errors()
            ], 422);

        }


        $user = User::create([

            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'status' => 'active'

        ]);


        // ربط المستخدم بالدور
        $user->roles()->attach($request->role_id);


        return response()->json([

            'message' => 'User created successfully',
            'data' => $user->load('roles')

        ], 201);
    }



    /**
     * Display a specific user.
     */
    public function show(string $id)
    {

        $user = User::with('roles')
            ->select('id', 'name', 'email', 'phone', 'status')
            ->find($id);


        if (!$user) {

            return response()->json([
                'message' => 'User not found'
            ], 404);

        }


        return response()->json([
            'status' => true,
            'data' => $user
        ]);

    }



    /**
     * Update user.
     */
    public function update(Request $request, string $id)
    {

        $user = User::find($id);


        if (!$user) {

            return response()->json([
                'message' => 'User not found'
            ], 404);

        }



        $validator = Validator::make($request->all(), [

            'name' => 'nullable|string|max:255',

            'email' => 'nullable|email|unique:users,email,' . $id,

            'phone' => 'nullable|unique:users,phone,' . $id,

            'password' => 'nullable|min:8',

            'status' => 'nullable|string|max:50',

            'role_id' => 'nullable|exists:roles,id'

        ]);



        if ($validator->fails()) {

            return response()->json([
                'errors' => $validator->errors()
            ], 422);

        }



        $user->update([

            'name' => $request->name ?? $user->name,

            'email' => $request->email ?? $user->email,

            'phone' => $request->phone ?? $user->phone,

            'status' => $request->status ?? $user->status,

        ]);



        // تحديث كلمة المرور
        if ($request->password) {

            $user->password = Hash::make($request->password);

            $user->save();

        }



        // تحديث Role
        if ($request->role_id) {

            $user->roles()->sync([
                $request->role_id
            ]);

        }



        return response()->json([

            'message' => 'User updated successfully',

            'data' => $user->load('roles')

        ]);

    }




    /**
     * Delete user.
     */
    public function destroy(string $id)
    {

        $user = User::find($id);


        if (!$user) {

            return response()->json([
                'message' => 'User not found'
            ], 404);

        }



        // حذف العلاقة من جدول role_user
        $user->roles()->detach();


        // حذف المستخدم
        $user->delete();



        return response()->json([

            'message' => 'User deleted successfully'

        ]);

    }

}
