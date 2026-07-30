<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    /**
     * Display a listing of roles.
     */
    public function index()
    {
        $roles = Role::with('users:id,name,email')->get();

        return response()->json([
            'status' => true,
            'data' => $roles
        ]);
    }


    /**
     * Store a newly created role.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'name' => 'required|string|max:100|unique:roles,name',

            'display_name' => 'required|array'

        ]);


        if ($validator->fails()) {

            return response()->json([
                'errors' => $validator->errors()
            ], 422);

        }


        $role = Role::create([

            'name' => $request->name,

            'display_name' => $request->display_name

        ]);


        return response()->json([

            'message' => 'Role created successfully',

            'data' => $role

        ], 201);
    }



    /**
     * Display a specific role.
     */
    public function show(string $id)
    {
        $role = Role::with('users:id,name,email')
                    ->find($id);


        if (!$role) {

            return response()->json([
                'message' => 'Role not found'
            ], 404);

        }


        return response()->json([

            'status' => true,

            'data' => $role

        ]);
    }



    /**
     * Update role.
     */
    public function update(Request $request, string $id)
    {

        $role = Role::find($id);


        if (!$role) {

            return response()->json([
                'message' => 'Role not found'
            ], 404);

        }


        $validator = Validator::make($request->all(), [

            'name' => 'nullable|string|max:100|unique:roles,name,' . $id,

            'display_name' => 'nullable|array'

        ]);


        if ($validator->fails()) {

            return response()->json([
                'errors' => $validator->errors()
            ], 422);

        }



        $role->update([

            'name' => $request->name ?? $role->name,

            'display_name' => $request->display_name ?? $role->display_name

        ]);



        return response()->json([

            'message' => 'Role updated successfully',

            'data' => $role

        ]);
    }



    /**
     * Delete role.
     */
    public function destroy(string $id)
    {

        $role = Role::find($id);


        if (!$role) {

            return response()->json([
                'message' => 'Role not found'
            ], 404);

        }


        // حذف العلاقات من جدول role_user
        $role->users()->detach();


        // حذف الدور
        $role->delete();



        return response()->json([

            'message' => 'Role deleted successfully'

        ]);
    }
}
